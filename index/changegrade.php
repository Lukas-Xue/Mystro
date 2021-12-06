<?php
    $student = $_REQUEST['student'];
    $sid = substr($_POST['id'], 0, 10);
    $aid = str_replace($sid, "", $_POST['id']);
    $grade = $_POST['grade'];
    $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
    if (mysqli_connect_errno()){
        printf("connection failed: %s\n", mysqli_connect_error());
        exit(1);
    }
    $query = "UPDATE grade SET grade = $grade where student_id = '$sid' and assignment_id = '$aid'";
    if (($result = mysqli_query($conn, $query)) == 0){
        printf("Error (update): %s\n", mysqli_error($conn));
        exit(1);
    }
    print("<h3>Assignment grade updated successfully.</h3>");
    print("<a href=\"./teach.php?student=$student\">back to teaching staff page</a>");
    mysqli_close($conn);
    mysqli_free_result($result);
?>