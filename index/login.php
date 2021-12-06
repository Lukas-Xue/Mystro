<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Mystro Login</title>
        <link rel="StyleSheet" href="login.css">
    </head>

    <body>
        <div class="login">
            <h1>Login</h1>
            <form method="post">
            <input type="text" name="student_id" placeholder="student ID" required="required" />
                <input type="password" name="login_id" placeholder="Login ID" required="required" />
                <button type="submit" class="btn btn-primary btn-block btn-large">Log On</button>
            </form>
        </div>

        <?php
            if (isset($_POST['student_id'])){
                $conn = mysqli_connect("localhost","cs377","ma9BcF@Y","mystro");
                $std_id = $_POST['student_id'];
                $login_id = $_POST['login_id'];
                $query = "select distinct login_id from students where student_id='$std_id'";
                if (($result = mysqli_query($conn, $query)) == 0){
                    printf("Error (create qa): %s\n", mysqli_error($conn));
                    exit(1);
                }
                while($row = mysqli_fetch_assoc($result)){
                    if ($row['login_id'] == $login_id){
                        Header("Location: index.php?student=".$std_id);
                    }
                    else {
                        ?>
                        <script>
                            alert("Student ID and Login ID do not match. Please try again.");
                        </script>
                        <?php
                    }
                }
            }
        ?>
    </body>
<html>