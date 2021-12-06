<?php
    $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
    $qa_id = $_POST['qa_id'];
    $std_id = $_POST['student'];
    $text = $_POST['reply'];
    date_default_timezone_set('America/New_York');
    $t=time();
    $time = date("Y-m-d h:i",$t);
    $query = "INSERT INTO reply (student_id, text, time, qa_id) VALUES ('$std_id', '$text', '$time', $qa_id)";
    if (($result = mysqli_query($conn, $query)) == 0){
        printf("Error (create qa): %s\n", mysqli_error($conn));
        exit(1);
    }
    print("<h3>Reply Posted!</h3>");
    print("<a href=\"./qa.php?student=$std_id\">back to QA page</a>");
    mysqli_close($conn);
    mysqli_free_result($result);
?>