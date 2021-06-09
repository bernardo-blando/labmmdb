<?php
session_start();

if (isset($_POST["username"]) && isset($_POST["password"])) {
    require_once "../connections/connection.php";
    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);
    $new_username = $_POST['username'];
    $username = $_SESSION['username_bb'];
    $password = $_POST['password'];
    $query = "SELECT password_hash  FROM mp_users WHERE username LIKE ?";

    if (mysqli_stmt_prepare($stmt, $query)) {

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_bind_result($stmt, $password_hash);

        mysqli_stmt_execute($stmt);


        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $password_hash)) {
                //sucesso password
                mysqli_stmt_close($stmt);
                $stmt1 = mysqli_stmt_init($link);

                $query1 = "
                    SELECT COUNT(username)
                    FROM mp_users
                    WHERE username LIKE ?";

                if (mysqli_stmt_prepare($stmt1, $query1)) {
                    mysqli_stmt_bind_param($stmt1, 's', $new_username);
                    mysqli_stmt_bind_result($stmt1, $userN);
                    mysqli_stmt_execute($stmt1);
                    mysqli_stmt_fetch($stmt1);

                } else {
                    echo "error description: " . mysqli_error($link);
                }
                mysqli_stmt_close($stmt1);

                if ($userN == 0) {
                    //sucesso
                    $stmt2 = mysqli_stmt_init($link);
                    $query2 = "
                        UPDATE mp_Users
                        SET username = ?
                        WHERE username = ? 
                   ";
                    if (mysqli_stmt_prepare($stmt2, $query2)){
                        mysqli_stmt_bind_param($stmt2, 'ss', $new_username, $username);
                        mysqli_stmt_execute($stmt2);
                        mysqli_stmt_fetch($stmt2);
                        mysqli_stmt_close($stmt2);
                        mysqli_close($link);
                        $_SESSION["username_bb"]=$new_username;
                        header("Location: ../home.php?feed3");
                    }else{
                        echo "error: ". mysqli_error($link);
                    }


                } else {
                    mysqli_close($link);
                    header("Location: ../home.php?feed=2");
                }


            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                echo "2";
                header("Location: ../home.php?feed=1");
            }
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            echo "3";
            header("Location: ../home.php?feed=0");
        }
    } else {
        echo "erro: " . mysqli_error($link);

    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);

}