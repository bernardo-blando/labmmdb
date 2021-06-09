<?php
session_start();
echo var_dump($_SESSION);
echo "<p>".var_dump($_POST)."</p>";

if (isset($_SESSION["username_bb"])&&isset($_SESSION["id_user_bb"])&&isset($_POST["comment"])&&isset($_GET["id"])&&isset($_GET["film"])){
    require_once "../connections/connection.php";
    $comment_id=$_GET["id"];
    $id_user=$_SESSION["id_user_bb"];
    $comment=$_POST["comment"];
    $id_film=$_GET["film"];
    echo $comment;

    $link=new_db_connection();
    $stmt=mysqli_stmt_init($link);

    $query="UPDATE mp_comments
            SET  comment = ?
            WHERE ref_idUsers= ? AND ref_idFilms = ? AND idComments= ?";

    if (mysqli_stmt_prepare($stmt, $query)){
        mysqli_stmt_bind_param($stmt, "siii", $comment, $id_user, $id_film, $comment_id);
        mysqli_stmt_execute($stmt);

        echo "comment_id: ".$comment_id. " id_user: ".$id_user . " comment: ".$comment . " id_film".  $id_film;


    }else{
        echo "erro: ".mysqli_error($link);
    }


    mysqli_stmt_close($stmt);
    mysqli_close($link);
    header("location: ../film.php?id=".$id_film);




}else{
    header("location: ../home.php?feed=0");
}