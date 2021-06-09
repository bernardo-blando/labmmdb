<?php
session_start();
echo var_dump($_SESSION);
echo "<p>".var_dump($_POST)."</p>";

if (isset($_SESSION["username_bb"])&&isset($_SESSION["id_user_bb"])&&isset($_POST["comment"])&&isset($_GET["id"])){
    require_once "../connections/connection.php";

    $user_id=$_SESSION["id_user_bb"];
    $comment=$_POST["comment"];
    $id_film=$_GET["id"];
echo $comment;
    $link=new_db_connection();
    $stmt=mysqli_stmt_init($link);
    $query="INSERT INTO mp_comments (ref_idUsers, comment, ref_idFilms)
            VALUES (?,?,?)";
    if (mysqli_stmt_prepare($stmt, $query)){
        mysqli_stmt_bind_param($stmt, "isi", $user_id,$comment,  $id_film);
        mysqli_stmt_execute($stmt);

        if(!mysqli_stmt_fetch($stmt)){
            mysqli_stmt_close($stmt);
            mysqli_close($link);

            header("location: ../home.php?feed=0");
        }
    }else{
        echo mysqli_error($link);
    }
     mysqli_stmt_close($stmt);
    mysqli_close($link);
    header("location: ../film.php?id=".$id_film);




}else{
    header("location: ../home.php?feed=0");
}