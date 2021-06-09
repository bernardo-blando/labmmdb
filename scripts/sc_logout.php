<?php
session_start();

Echo"<h1>Algo está errado <a href='../home.php'>voltar</a></h1>";

if (isset($_SESSION["id_user_bb"])&&isset($_SESSION["username_bb"])){
    session_destroy();
    header("location: ../login.php");
}

session_destroy();
