<?php
session_start();
if (isset($_GET["id"])) {
    if (isset($_GET["rate"])) {

        $rate = round($_GET["rate"]);
        $film = $_GET["id"];
        $user_id= $_SESSION["id_user_bb"];
        if ($rate > 0 && $rate <= 5) {
            require_once "../connections/connection.php";
            $link=new_db_connection();
            $stmt=mysqli_stmt_init($link);
            $query="INSERT INTO mp_ratings (ref_idFilms, ref_idUsers, Rating)
                    VALUES (?,?,?)";
            if (mysqli_stmt_prepare($stmt, $query)){
                mysqli_stmt_bind_param($stmt, "iii", $film, $user_id, $rate );
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);


            }else{
                mysqli_stmt_close($stmt);

                echo "erro: ". mysqli_error($link);
            }


        }


        if (isset($_SESSION["avg_rating"]) && ($_SESSION["avg_rating"] <= 5 || $_SESSION["avg_rating"] >= 0)) {
            $avg_rating=$_SESSION["avg_rating"];
            $stmt1 = mysqli_stmt_init($link);
            $query1 = "  UPDATE mp_films
                        SET  avg_rating= ?
                        WHERE  idFilms=?";
            if (mysqli_stmt_prepare($stmt1, $query1)) {
                mysqli_stmt_bind_param($stmt1, "di", $avg_rating, $film);
                if (!mysqli_stmt_execute($stmt1)) {

                    echo mysqli_error($link);
                };

                mysqli_stmt_close($stmt1);


            } else {
                echo "erro: " . mysqli_error($link);
            }


        }
        mysqli_close($link);
        header("Location: ../film.php?id=" . $_GET["id"]);
    }


}else{
header("location: ../home.php");}