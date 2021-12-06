<?php
    $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
    $course = $_POST['course'];
    $title = $_POST['title'];
    $std_id = $_POST['student'];
    $text = $_POST['text'];
    date_default_timezone_set('America/New_York');
    $t=time();
    $time = date("Y-m-d h:i",$t);

    $query = "INSERT INTO QA (course_id, title, text, post_date, std_id) VALUES ($course, '$title', '$text', '$time', '$std_id')";
    if (($result = mysqli_query($conn, $query)) == 0){
        printf("Error (create qa): %s\n", mysqli_error($conn));
        exit(1);
    }
    if (($result = mysqli_query($conn, "SELECT MAX(qa_id) from QA")) == 0){
        printf("Error (getting qa id): %s\n", mysqli_error($conn));
        exit(1);
    }
    while($row = mysqli_fetch_assoc($result)){
        $qa_id = $row['MAX(qa_id)'];
    }
    foreach($_POST['tag'] as $tag){
        if (($result = mysqli_query($conn, "INSERT INTO qa_category (qa_id, tag_id) VALUES ($qa_id, $tag)")) == 0){
            printf("Error (insert tag): %s\n", mysqli_error($conn));
            exit(1);
        }
    }
    print('<h3>Question Posted! Now grab a coffee and relax while waiting for answer!</h3>');
    print("<a href=\"./qa.php?student=$std_id\">back to QA page</a>");
    mysqli_close($conn);
    mysqli_free_result($result);
?>