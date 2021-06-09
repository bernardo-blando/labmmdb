<?php

require_once "../connections/connection.php";

if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["password_confirmation"])) {
    if ($_POST["password"] == $_POST["password_confirmation"]) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $link = new_db_connection();


        //STMT para garantir username unico
        $stmt1 = mysqli_stmt_init($link);

        $query1 = "
        SELECT COUNT(username)
        FROM mp_users
        WHERE username LIKE ?";

        if (mysqli_stmt_prepare($stmt1, $query1)) {
            mysqli_stmt_bind_param($stmt1, 's', $username);
            mysqli_stmt_bind_result($stmt1, $userN);
            mysqli_stmt_execute($stmt1);
            mysqli_stmt_fetch($stmt1);

        } else {
            echo "error description: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt1);


        //STMT para garantir email unico


        $stmt2 = mysqli_stmt_init($link);

        $query2 = "
        SELECT COUNT(email)
        FROM mp_users
        WHERE email LIKE ?";

        if (mysqli_stmt_prepare($stmt2, $query2)) {
            mysqli_stmt_bind_param($stmt2, 's', $email);
            mysqli_stmt_bind_result($stmt2, $emailN);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_fetch($stmt2);
            mysqli_stmt_close($stmt2);
        } else {
            echo "error description: " . mysqli_error($link);
        }


        if ($userN == 0) {

            if ($emailN == 0) {

                $stmt = mysqli_stmt_init($link);
                $query = "INSERT INTO mp_users (username, email, password_hash) VALUES (?,?,?)";
                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $password_hash);

                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);

                    header("Location: ../login.php?msg=1");
                    echo "sucess";

                } else {
                    echo "signUp falhou";
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    header("Location: ../signup.php?msg=0");
                    echo "error description: " . mysqli_error($link);

                }
            } else {
                mysqli_close($link);
                header("Location: ../signup.php?msg=5");
                echo "email  existente";

            }
        } else {

            mysqli_close($link);
            header("Location: ../signup.php?msg=4");
            echo "usernames a mais";

        }
    } else {
    echo "passwords diferentes";
        header("Location: ../signup.php?msg=6");
    }
} else {
    echo "n ha posts";
    header("Location: ../signup.php?msg=0");
}

