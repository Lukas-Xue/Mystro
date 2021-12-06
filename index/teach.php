<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Mystro TEACHING STAFF</title>
        <link rel="StyleSheet" href="stylesheet.css">
    </head>

    <body>
        <script>
            var jsonObj;
            var new_course;
            var course_modal;
            var jsonObj_2;
            var course_content;
            var foo = "f";
            var foo2 = "ff"
            var tmp;
            var grade;
            function overlay_on(){
                tmp = document.getElementById(event.srcElement.id.replace("f", ""));
                if (tmp.offsetHeight === 0 && tmp.offsetWidth === 0){
                    tmp.style.display = "block";
                    document.getElementById("overlay").style.display = "block";
                    document.body.style.overflow = "hidden";
                    document.getElementById(foo2 + event.srcElement.id.replace("f", "")).style.display = "block";

                    let elements = document.getElementsByClassName("course_box");
                    for (let i = 0; i < elements.length; i++){
                        elements[i].style.display='none';
                    }
                    document.getElementById('close').style.display = 'block';
                } else{
                    tmp.style.display = "none";
                }
            }
            function overlay_off(){
                document.getElementById("overlay").style.display = "none";
                tmp = document.getElementsByClassName("modal");
                for(let i = 0; i < tmp.length; i++) { 
                    tmp[i].style.display='none';
                }
                document.body.style.overflow = "auto";
            }
            function list_course(){
                tmp = document.getElementsByClassName("course_box");
                for (let i = 0; i < tmp.length; i++){
                    tmp[i].style.display = "inline-block";
                }
                tmp = document.getElementsByClassName("grade");
                for (let i = 0; i < tmp.length; i++){
                    tmp[i].style.display='none';
                }
                document.getElementById("close").style.display = "none";
            }
        </script>
        <?php
            $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
            if (mysqli_connect_errno()){
                printf("connection failed: %s\n", mysqli_connect_error());
                exit(1);
            }
            $student = $_REQUEST['student'];
            $query = "select distinct fname, lname from students where student_id='$student'";
            if (($result = mysqli_query($conn, $query)) == 0){
                printf("Error: %s\n", mysqli_error($conn));
                exit(1);
            }
            while($row = mysqli_fetch_assoc($result)){
                $fname = $row['fname'];
                $lname = $row['lname'];
            }
            print("<h3>Welcome, $fname $lname</h3>");
        ?>

        <div id="application">
            <?php
                print("<a href=\"./index.php?student=$student\">student homepage</a>");
                print("<a href=\"./teach.php?student=$student\">teaching staff</a>");
                print("<a href=\"./qa.php?student=$student\">Q&A</a>");
                print("<a href=\"./transcript.php?student=$student\">transcript</a>");
            ?>
        </div>
        <div class="logout">
            <form action="login.php" method="POST">
                <input type="submit" value="Log out">
            </form>
        </div>
        <div id="close" onclick="list_course()">Close Grade</div>
        <div id="courses">
            <!-- PHP that connects to mysql database and fetch the user courses -->
            <?php
                $query = "(select course.course_id, number, name, year, semester, instructor_id from course, course_name 
                where course.course_id = course_name.course_id and course.instructor_id = '$student') 
                UNION
                (select c.course_id, number, name, year, semester, instructor_id from ta t, course c, course_name 
                where t.course_id = c.course_id and c.course_id = course_name.course_id and t.ta_id = '$student')";
                if (($result = mysqli_query($conn, $query)) == 0){
                    printf("Error: %s\n", mysqli_error($conn));
                    exit(1);
                }
                while($row = mysqli_fetch_assoc($result)){
                    ?>
                    <!-- for each course, create a box for display purpose -->
                    <script>
                        jsonObj = <?php echo json_encode($row); ?>;
                        new_course = document.createElement('div');
                        if (jsonObj.instructor_id === `<?php echo $student; ?>`){
                            new_course.className = "course_completed";
                            new_course.innerHTML = jsonObj.number + '\n' + "Instructor";
                        } else {
                            new_course.className = "course_inprogress";
                            new_course.innerHTML = jsonObj.number + '\n' + "TA";
                        }
                        new_course.classList.add("course_box");
                        document.getElementById('courses').appendChild(new_course);
                        new_course.setAttribute('id', foo + jsonObj.course_id);
                        // create assignment page
                        course_modal = document.createElement('div');
                        course_modal.innerHTML = "<h2>"+jsonObj.number+' '+jsonObj.name+' '+jsonObj.year +' '+jsonObj.semester+"</h2><h3>Enrolled Students:</h3>";
                    </script>

                        <!-- php for getting course content -->
                        <?php
                            foreach($row as $key => $value){
                                if ($key == "course_id"){
                                    print("<div id=$value class='grade'>");
                                    $query_ = "select a.assignment_id, a.name, s.student_id, s.fname, s.lname, g.grade from grade g, assignment a, course c, students s where g.student_id=s.student_id and g.assignment_id=a.assignment_id and a.course_id=c.course_id and c.course_id=$value";
                                    if (($grade_result = mysqli_query($conn, $query_)) == 0){
                                        printf("Error: %s\n", mysqli_error($conn));
                                        exit(1);
                                    }
                                    $header = false;
                                    print("<table><div>");
                                    while($row_grade = mysqli_fetch_assoc($grade_result)){
                                        if (!$header){
                                            $header = true;
                                            print("<thead><tr>\n");
                                            foreach ($row_grade as $key_ => $value_){
                                                if ($key_ == "assignment_id"){
                                                    continue;
                                                }
                                                print "<th>" . $key_ . "</th>";
                                            }
                                            print("</tr></thead>\n");
                                        }
                                        print("<tr>\n");
                                        $aid = $row_grade['assignment_id'];
                                        $sid = $row_grade['student_id'];
                                        $id = $sid . (string)$aid;
                                        foreach ($row_grade as $key_ => $value_){
                                            if ($key_=="assignment_id"){
                                                continue;
                                            }
                                            if ($key_ == "grade"){
                                                print("<td><form action=\"changegrade.php?student=$student\" method=\"POST\">
                                                        <input type=\"number\" name=\"grade\" class=\"editable change\" placeholder=\"$value_\">
                                                        <input type=\"radio\" name=\"id\" value=\"$id\" style=\"display:none;\" checked>
                                                        <input type=\"submit\" style=\"display:none;\">
                                                        </form></td>");
                                            }
                                            else {
                                                print("<td>" . $value_ . "</td>");
                                            }
                                        }
                                        print("</tr>\n");
                                    }
                                    print("</table></div>");
                                    $query = "select s.student_id, fname, lname, t.letter_grade, t.course_id from take t, students s where t.student_id = s.student_id and t.course_id = $value";
                                    if (($result_students = mysqli_query($conn, $query)) == 0){
                                        printf("Error: %s\n", mysqli_error($conn));
                                        exit(1);
                                    }
                                    while($row_students = mysqli_fetch_assoc($result_students)){
                        ?>
                                        <script>
                                            jsonObj_2 = <?php echo json_encode($row_students); ?>;
                                            course_modal.innerHTML += `${jsonObj_2.student_id} <b>${jsonObj_2.fname} ${jsonObj_2.lname} </b>${jsonObj_2.letter_grade} 
                                            <form action="letter_grade.php?student=<?php echo $student; ?>" method="POST"><select name="grade">
                                                <option value="A">A</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B">B</option>
                                                <option value="B-">B-</option>
                                                <option value="C+">C+</option>
                                                <option value="C">C</option>
                                                <option value="C-">C-</option>
                                                <option value="D+">D+</option>
                                                <option value="D">D</option>
                                                <option value="IP">In Progress</option>
                                            </select>
                                            <input type="radio" name="id" value="${jsonObj_2.student_id}${jsonObj_2.course_id}" style="display:none;" checked>
                                            <input type="submit">
                                            <br>`;
                                        </script>
                        <?php
                                    }
                        ?>
                                    <script>
                                        course_modal.innerHTML += "<h3>Assignments:</h3>";
                                    </script>
                        <?php
                                    $query = "select * from assignment a where a.course_id = $value";
                                    if (($result_assignment = mysqli_query($conn, $query)) == 0){
                                        printf("Error: %s\n", mysqli_error($conn));
                                        exit(1);
                                    }
                                    while($row_assignment = mysqli_fetch_assoc($result_assignment)){
                        ?>
                                        <script>
                                            jsonObj_2 = <?php echo json_encode($row_assignment); ?>;
                                            course_modal.innerHTML += "<h4>"+jsonObj_2.name+":</h4> <b>total points:</b> "+jsonObj_2.total_pts
                                            +"<br><b>due:</b> "+jsonObj_2.due+"<br><b>description:</b> "+jsonObj_2.text+"<br>";
                                        </script>
                        <?php
                                    }
                                    break;
                                }
                            }
                        ?>

                    <script>
                        course_modal.className = "modal";
                        course_modal.setAttribute('id', foo2 + jsonObj.course_id);
                        document.body.appendChild(course_modal);
                        new_course.addEventListener("click", overlay_on);
                    </script>
                    <?php
                }
            ?>
        </div>
        <div id="overlay" onclick="overlay_off()"></div>

        <!-- add assignment -->
        <div class="form add_assignment">
                <h3>Add Assignment(Teaching Staff ONLY): </h3>
                <form action="form.php?student=<?php echo $student; ?>" method="POST">
                    <?php
                        $query = "select distinct c.course_id, c.number, c.semester, c.year, n.name from course_name n, course c where n.course_id = c.course_id and c.instructor_id = '$student'
                                    UNION
                                  select distinct c.course_id, c.number, c.semester, c.year, n.name from course_name n, course c, ta where n.course_id = c.course_id and ta.course_id = c.course_id and ta.ta_id = '$student'";
                        if (($result = mysqli_query($conn, $query)) == 0){
                            printf("Error: %s\n", mysqli_error($conn));
                            exit(1);
                        }
                        while($row = mysqli_fetch_assoc($result)){
                            print("<input type=\"radio\" name=\"class\" value=\"" . $row[course_id] . "\">" . $row[number]." ".$row[name]." ".$row[semester]." ".$row[year]."<br>");
                        }
                    ?>
                    <input type="text" name="assignment_name" class="input" placeholder="Assignment Name (NO DUPLICATES with other assignment of same course)"><br>
                    <input type="number" name="total_points" class="input" step="1" placeholder="Total Points"><br>
                    Due year: 
                    <select name="year">
                    <?php
                        for ($i = 2016; $i <= 2023; $i++){
                            print("<option value=\"$i\">$i</option>");
                        }
                    ?>
                    </select>
                    Due month:
                    <select name="month">
                    <?php
                        for ($i = 1; $i <= 12; $i++){
                            print("<option value=\"$i\">$i</option>");
                        }
                    ?>
                    </select>
                    Due date:
                    <select name="date">
                    <?php
                        for ($i = 1; $i <= 31; $i++){
                            print("<option value=\"$i\">$i</option>");
                        }
                    ?>
                    </select>
                    Due hour:
                    <select name="hour">
                    <?php
                        for ($i = 0; $i <= 23; $i++){
                            print("<option value=\"$i\">$i</option>");
                        }
                    ?>
                    </select>
                    Due minute:
                    <select name="minute">
                    <?php
                        for ($i = 0; $i <= 59; $i++){
                            print("<option value=\"$i\">$i</option>");
                        }
                    ?>
                    </select><br>
                    <TEXTAREA name="text" rows="10" cols="60" placeholder="Assignment Description"></TEXTAREA>
                    <br>
                    <br>
                    <input type="submit">
                </form>
        </div>
        <?php
            mysqli_close($conn);
            mysqli_free_result($result);
            mysqli_free_result($result_assignment);
            mysqli_free_result($result_students);
            mysqli_free_result($grade_result);
        ?>
    </body>
</html>