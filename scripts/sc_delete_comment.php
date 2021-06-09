<?php
session_start();
echo var_dump($_SESSION);
echo var_dump($_GET);

if (isset($_SESSION["username_bb"])&&isset($_SESSION["id_user_bb"])&&isset($_GET["id"])&&isset($_GET["film"])){

    $id_user=$_SESSION["id_user_bb"];
    $id_comment=$_GET["id"];
    $id_film=$_GET["film"];
    require_once "../connections/connection.php";
    $link=new_db_connection();
    $stmt=mysqli_stmt_init($link);
    $query="DELETE FROM mp_comments
            WHERE idComments = ? AND ref_idUsers = ? AND ref_idFilms = ?";     //proibido malandricesss :DD
    if (mysqli_stmt_prepare($stmt, $query)){
        mysqli_stmt_bind_param($stmt, "iii", $id_comment, $id_user, $id_film);
        mysqli_stmt_execute($stmt);

    }else{
        echo mysqli_error($link);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    header("location: ../film.php?id=".$id_film);
}else{
echo"1";
header("location: ../home.php?feed=0");}