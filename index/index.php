<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Mystro HOMEPAGE</title>
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
                document.getElementById("overlay").style.display = "block";
                document.getElementById(foo2 + event.srcElement.id.replace("f", "")).style.display = "block";
            }
            function overlay_off(){
                document.getElementById("overlay").style.display = "none";
                tmp = document.getElementsByClassName("modal");
                for(var i=0; i<tmp.length; i++) { 
                    tmp[i].style.display='none';
                }
            }
        </script>
        <?php
            $student = $_REQUEST['student'];
            $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
            if (mysqli_connect_errno()){
                printf("connection failed: %s\n", mysqli_connect_error());
                exit(1);
            }
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

        <div id="courses">
            <!-- PHP that connects to mysql database and fetch the user courses -->
            <?php
                $query = "select course.course_id, number, name, semester, year, letter_grade 
                from course, course_name c, take t where c.course_id = course.course_id and t.course_id = c.course_id and t.student_id = '$student'";
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
                        if (jsonObj.letter_grade === 'IP'){
                            new_course.className = "course_inprogress";
                        } else {
                            new_course.className = "course_completed";
                        }
                        new_course.classList.add("course_box");
                        new_course.innerHTML = jsonObj.number + '\n' + jsonObj.letter_grade;
                        document.getElementById('courses').appendChild(new_course);
                        new_course.setAttribute('id', foo + jsonObj.course_id);
                        // create assignment page
                        course_modal = document.createElement('div');
                        course_modal.innerHTML = "<h2>"+jsonObj.name+' '+jsonObj.year +' '+jsonObj.semester+"</h2><br>";
                    </script>
                        <!-- php for getting course content -->
                        <?php
                            foreach($row as $key => $value){
                                if ($key == "course_id"){
                                    $query = "select a.name, a.total_pts, a.due, a.text, g.grade from grade g, assignment a 
                                    where g.assignment_id = a.assignment_id and g.student_id = '$student' and a.course_id = $value";
                                    if (($result_assignment = mysqli_query($conn, $query)) == 0){
                                        printf("Error: %s\n", mysqli_error($conn));
                                        exit(1);
                                    }
                                    while($row_assignment = mysqli_fetch_assoc($result_assignment)){
                        ?>
                                        <script>
                                            jsonObj_2 = <?php echo json_encode($row_assignment); ?>;
                                            if (jsonObj_2.grade === null){
                                                grade = "in progress";
                                            } else {
                                                grade = jsonObj_2.grade;
                                            }
                                            course_modal.innerHTML += "<h3>"+jsonObj_2.name+":</h3><b>due:</b> "+jsonObj_2.due+"<br><b>description:</b> "+jsonObj_2.text+"<br><b>total points:</b> "+jsonObj_2.total_pts
                                            +"<br><b>your grade:</b> "
                                            +grade+"<br>==========================<br>";
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
                mysqli_close($conn);
                mysqli_free_result($result);
                mysqli_free_result($result_assignment);
            ?>
        </div>
        <div id="overlay" onclick="overlay_off()"></div>
    </body>
</html>