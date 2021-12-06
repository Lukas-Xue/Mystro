<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Mystro HOMEPAGE</title>
        <link rel="StyleSheet" href="stylesheet.css">
    </head>

    <body>
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
            print("<h3>Welcome, $fname $lname, here is your transcript</h3>");
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

        <?php
            $query = "select number, name, semester, year, letter_grade from course, take, course_name where course.course_id = course_name.course_id and take.student_id = '$student' and take.course_id = course.course_id";
            if (($result = mysqli_query($conn, $query)) == 0){
                printf("Error: %s\n", mysqli_error($conn));
                exit(1);
            }
            print("<table>\n");
            $header = false;
            while ($row = mysqli_fetch_assoc($result)){
                if (!$header){
                    $header = true;
                    print("<thead><tr>\n");
                    foreach ($row as $key => $value){
                        print "<th>" . $key . "</th>";
                    }
                    print("</tr></thead>\n");
                }
                print("<tr>\n");
                foreach ($row as $key => $value){
                    print("<td>" . $value . "</td>");
                }
                print("</tr>\n");
            }
            print("</table>\n");
            mysqli_close($conn);
            mysqli_free_result($result);
        ?>
    </body>