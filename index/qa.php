<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Mystro QA</title>
        <link rel="StyleSheet" href="stylesheet.css">
    </head>

    <body>
        <script>
            var tmp;
            var elements;
            var hide;
            var jsonObj;
            var new_thread;
            var qa_id;
            var flag;
            function onclickhandler(){
                hide = document.getElementsByClassName('hidden');
                for (var i=0; i<hide.length; i++){
                    hide[i].style.display='none';
                }
                tmp = document.getElementsByClassName('tag');
                for (var i=0; i<tmp.length; i++){
                    if (tmp[i].checked){
                        elements = document.getElementsByClassName(`tag${tmp[i].value}`);
                        for (var k=0; k<elements.length; k++){
                            elements[k].style.display='block';
                        }
                    }
                }
            }

            function overlay_on(){
                document.getElementById("overlay").style.display = "block";
                document.getElementById(`thread${event.srcElement.id.replace("title", "")}`).style.display = "block";
            }

            function overlay_off(){
                document.getElementById("overlay").style.display = "none";
                tmp = document.getElementsByClassName("modal");
                for(var i=0; i<tmp.length; i++) { 
                    tmp[i].style.display='none';
                }
            }
        </script>
        <div class="logout">
            <form action="login.php" method="POST">
                <input type="submit" value="Log out">
            </form>
        </div>
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
        <form action='qa.php?student=<?php echo $student; ?>' method="POST">
            <select class="class" name="course" onchange="this.form.submit()">
                <option disabled hidden selected>Choose a course</option>
            <?php
                $query = "select distinct c.course_id, c.number, n.name, c.semester, c.year from ((select course_id from take where student_id='$student') 
                UNION (select course_id from course where instructor_id='$student') 
                UNION (select course_id from ta where ta_id='$student')) as s, course c, course_name n where s.course_id = c.course_id and c.course_id = n.course_id";
                if (($result = mysqli_query($conn, $query)) == 0){
                    printf("Error: %s\n", mysqli_error($conn));
                    exit(1);
                }
                while($row = mysqli_fetch_assoc($result)){
                    $course_id = $row['course_id'];
                    $number = $row['number'];
                    $name = $row['name'];
                    $sem = $row['semester'];
                    $year = $row['year'];
                    print("<option value=\"$course_id\">$number $name $sem $year</option>");
                }
            ?>
            </select>
        </form>
        <?php
            if (isset($_POST['course'])){
                $course = $_POST['course'];
                $query = "select distinct c.number, c.semester, c.year, n.name from course c, course_name n where c.course_id=n.course_id and c.course_id=$course";
                if (($result_course = mysqli_query($conn, $query)) == 0){
                    printf("Error: %s\n", mysqli_error($conn));
                    exit(1);
                }
                while($row_course = mysqli_fetch_assoc($result_course)){
                    $number=$row_course['number'];
                    $name=$row_course['name'];
                    $sem=$row_course['semester'];
                    $year=$row_course['year'];
                }
                print("<h2>Welcome to $number $name $sem $year Q&A Page!</h2>");
            }
        ?>
        <div class="side-bar">
            <?php
            if (isset($_POST['course'])){
                $course = $_POST['course'];
                $query = "select * from tag_category where course_id = $course";
                if (($result_qa = mysqli_query($conn, $query)) == 0){
                    printf("Error: %s\n", mysqli_error($conn));
                    exit(1);
                }
                while($row_qa = mysqli_fetch_assoc($result_qa)){
                    $tag_name = $row_qa['tag'];
                    $value = $row_qa['tag_id'];
                    print("<input type=\"checkbox\" name=\"tag[]\" value=\"$value\" onclick=\"onclickhandler()\" class=\"tag\" checked>$tag_name<br>");
                }
            }
            ?>
        </div>
        <div class="main-content">
            <?php
            if (isset($_POST['course'])){
                $course = $_POST['course'];
                $query = "select tag_id, tag from tag_category where course_id = $course";
                if (($result_qa = mysqli_query($conn, $query)) == 0){
                    printf("Error: %s\n", mysqli_error($conn));
                    exit(1);
                }
                while($row_qa = mysqli_fetch_assoc($result_qa)){
                    $value = $row_qa['tag_id'];
                    $tag = $row_qa['tag'];
                    $query_2 = "select * from students, QA, qa_category q where students.student_id=QA.std_id and QA.qa_id=q.qa_id and QA.course_id=\"$course\" and q.tag_id=\"$value\" order by post_date desc";
                    if (($result_content = mysqli_query($conn, $query_2)) == 0){
                        printf("Error: %s\n", mysqli_error($conn));
                        exit(1);
                    }
                    print("<div class=\"tag$value hidden\"><div class=\"label\">$tag</div>");
                    while($row_content = mysqli_fetch_assoc($result_content)){
                        print("<div class=\"post-box\">");
                        $title = $row_content['title'];
                        $text = $row_content['text'];
                        $post_date = $row_content['post_date'];
                        $fname = $row_content['fname'];
                        $lname = $row_content['lname'];
                        $qaid = $row_content['qa_id'];
                        print("<div class=\"text-title\" id=\"title$qaid\">$title</div>");
                        print("<div class=\"text-secondary\">$text</div>");
                        print("<div class=\"text-secondary\">Posted on: $post_date by $fname $lname</div>");
                        print("</div>");
                        $query_3 = "select distinct * from reply, students where students.student_id=reply.student_id and qa_id=$qaid order by time asc";
                        if (($result_reply = mysqli_query($conn, $query_3)) == 0){
                            printf("Error: %s\n", mysqli_error($conn));
                            exit(1);
                        }
                        ?>
                        <script>
                            new_thread = document.createElement('div');
                            flag = false;
                        </script>
                        <?php
                        while($row_reply = mysqli_fetch_assoc($result_reply)){
                            ?>
                            <script>
                                flag = true;
                                jsonObj = <?php echo json_encode($row_reply); ?>;
                                new_thread.innerHTML += `<div class="post-box">
                                                            ${jsonObj.text}<br>
                                                            Posted on: ${jsonObj.time} by ${jsonObj.fname} ${jsonObj.lname}
                                                        </div>`;
                            </script>
                            <?php
                        }
                        ?>
                        <script>
                            if (flag===false){
                                new_thread.innerHTML += 'no reply yet! be the first one to comment';
                            }
                            new_thread.className='modal';
                            new_thread.setAttribute('id', `thread<?php echo $qaid; ?>`);
                            new_thread.innerHTML += `<div">
                                                        <form action="add_reply.php" method="POST">
                                                            <TEXTAREA name="reply" rows='5' cols='60' placeholder='Your reply'></TEXTAREA><br>
                                                            <input type='radio' name='qa_id' value=<?php echo $qaid; ?> style='display:none;' checked>
                                                            <input type='radio' name='student' value='<?php echo $student; ?>' style='display:none;' checked>
                                                            <input type='submit'>
                                                        </form>
                                                     </div>`;
                            document.body.appendChild(new_thread);
                        </script>
                        <?php
                    }
                    print("</div>");
                }
            }
            ?>
        </div>
        <div id="overlay" onclick="overlay_off()"></div>

        <div class="add-qa">
            <div class="form">
                <form action="add_qa.php" method="POST">
                    <?php
                        if (isset($_POST['course'])){
                            print("<h4>Add a question for your instructor or classmates!</h4>");
                            print("<input type=\"text\" name=\"title\" class=\"input\" placeholder=\"Title\"><br>");
                            $course = $_POST['course'];
                            $query = "select distinct * from tag_category t where t.course_id=$course";
                            if (($result = mysqli_query($conn, $query)) == 0){
                                printf("Error: %s\n", mysqli_error($conn));
                                exit(1);
                            }
                            while($row = mysqli_fetch_assoc($result)){
                                $tag_name = $row['tag'];
                                $tag_id = $row['tag_id'];
                                print("<input type=\"checkbox\" name=\"tag[]\" value=\"$tag_id\">$tag_name<br>");
                            }
                            print("<input type=\"radio\" name=\"course\" value=\"$course\" style=\"display:none;\" checked>");
                            print("<input type=\"radio\" name=\"student\" value=\"$student\" style=\"display:none;\" checked>");
                            print("<TEXTAREA name=\"text\" rows=\"10\" cols=\"60\" placeholder=\"Description\"></TEXTAREA><br>");
                            print("<input type=\"submit\">");
                        }
                    ?>
            </div>
        </div>

        <script>
            tmp = document.getElementsByClassName('text-title');
            for (var i=0; i<tmp.length; i++){
                tmp[i].addEventListener("click", overlay_on);
            }
        </script>

        <?php
            mysqli_close($conn);
            mysqli_free_result($result);
            mysqli_free_result($result_course);
            mysqli_free_result($result_qa);
            mysqli_free_result($result_content);
            mysqli_free_result($result_reply);
        ?>
    </body>
</html>