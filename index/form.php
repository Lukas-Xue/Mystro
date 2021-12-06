<?php
    $student = $_REQUEST['student'];
    $course_id = (int)$_POST['class'];
    $aname = $_POST['assignment_name'];
    $total_pts = (int)$_POST['total_points'];
    $text = $_POST['text'];
    $year = $_POST['year'];
    $month = $_POST['month'];
    $date = $_POST['date'];
    $hour = $_POST['hour'];
    $minute = $_POST['minute'];
    $timestamp = date((string)$year.'-'.(string)$month.'-'.(string)$date.' '.(string)$hour.':'.(string)$minute);
    $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
    if (mysqli_connect_errno()){
        printf("connection failed: %s\n", mysqli_connect_error());
        exit(1);
    }
    $query = "INSERT INTO assignment (name, total_pts, text, due, course_id) VALUES ('$aname', $total_pts, '$text', '$timestamp', $course_id)";
    if (($result = mysqli_query($conn, $query)) == 0){
        printf("Error (create assignment): %s\n", mysqli_error($conn));
        exit(1);
    }
    if (($result = mysqli_query($conn, "SELECT MAX(assignment_id) from assignment")) == 0){
        printf("Error (getting assignment id): %s\n", mysqli_error($conn));
        exit(1);
    }
    while($row = mysqli_fetch_assoc($result)){
        $assignment_id = $row['MAX(assignment_id)'];
    }
    if (($result = mysqli_query($conn, "select student_id from take where take.course_id = $course_id")) == 0){
        printf("Error (getting student id for the course): %s\n", mysqli_error($conn));
        exit(1);
    }
    while($row = mysqli_fetch_assoc($result)){
        $student_id = $row['student_id'];
        if (($result_insert_null = mysqli_query($conn, "INSERT INTO grade (assignment_id, grade, student_id) VALUES ($assignment_id, NULL, '$student_id')")) == 0){
            printf("Error (insert null for students in that course): %s\n", mysqli_error($conn));
            exit(1);
        }
    }
    print("<h3>Assignment $aname Created Successfully.</h3>");
    print("<a href=\"./teach.php?student=$student\">back to teaching staff page</a>");
    mysqli_close($conn);
    mysqli_free_result($result);
    mysqli_free_result($result_insert_null);
?>