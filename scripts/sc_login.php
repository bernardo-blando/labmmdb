<?php
session_start();
require_once "../connections/connection.php";
if (isset($_POST['username']) && isset($_POST['password'])) {
    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "SELECT password_hash,  idUsers  FROM mp_users WHERE username LIKE ?";

    if (mysqli_stmt_prepare($stmt, $query)) {

        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_bind_result($stmt, $password_hash, $id_users);

        mysqli_stmt_execute($stmt);


        if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $password_hash)) {
                $_SESSION["username_bb"] = $username;
                $_SESSION["id_user_bb"] = $id_users;
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                echo "1";
                header("Location: ../home.php");

            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                echo "2";
                header("Location: ../login.php?msg=2");
            }
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            echo "3";
           header("Location: ../login.php?msg=2");
        }
    } else {
        echo "erro: " . mysqli_error($link);
    }
}else {
    echo "4";
header("Location: ../login.php?msg=2");
}