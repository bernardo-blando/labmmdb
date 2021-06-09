<!-- Navigation -->
<?php
session_start();

if (isset($_GET["feed"])) {
    $go = true;
} else {
    $go = false;
}

?>
<style>
    .dropdown-item:hover{background-color: black;
        color:#f8f9fa;}
    .dropdown-item{color:#f8f9fa;}
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand text-warning" href="home.php">l<span style="color: gray !important;">abm</span>MDb</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home
                        <span class="sr-only"></span>
                    </a>
                </li>


                        <?php
                        require_once "connections/connection.php";
                        $link=new_db_connection();
                        $stmtNav=mysqli_stmt_init($link);
                        $queryNav="SELECT idGeneros, genero
                                    FROM mp_generos";
                        if (mysqli_stmt_prepare($stmtNav, $queryNav)){
                            mysqli_stmt_execute($stmtNav);
                            mysqli_stmt_bind_result($stmtNav, $id_genero, $genero);
                            ;
                            echo '<li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Categorias<span class="caret"></span></a>
                                    <ul class="dropdown-menu bg-dark text-light" aria-labelledby="navbarDropdownMenuLink">';

                            while (mysqli_stmt_fetch($stmtNav)){
                                echo '<li><a class="dropdown-item  text light"  href="categorias.php?id='.$id_genero.'" >'.$genero.'</a></li>';
                            }
                                echo '</ul>
                                    </li>';
                            mysqli_stmt_close($stmtNav);
                            mysqli_close($link);

                        }else{
                            echo mysqli_error($link);
                        }

                        ?>


                <?php
                if (isset($_SESSION["username_bb"])) {
                    echo '<li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">' . $_SESSION["username_bb"] . '<span class="caret"></span></a>
                    <ul class="dropdown-menu bg-dark text-light" aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item  text light"  data-toggle="modal" data-target="#mudar_nome">Mudar Username</a></li>
                        <li><a class="dropdown-item  text-light"  href="scripts/sc_logout.php">Logout</a></li>
                    </ul>
                </li>';
                } else {
                    echo '<li class="nav-item">
                    <a class="nav-link" href="login.php">LogIn/Registo</a>
                </li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
<div class="modal fade" id="mudar_nome" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title " id="exampleModalLabel">Mudar de Username</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fecha">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <form method="post" action="scripts/sc_mudar_nome.php">

                    <p><input type="text" name="username" placeholder="Novo Username"></p>
                    <p><input type="password" name="password" placeholder="password"></p>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="submit btn btn-warning">Submeter</button>
                </form> </div>
        </div>
    </div>
</div>
<?php
if (isset($_GET["feed"])) {
    $msg_show = true;
    switch ($_GET["feed"]) {
        case 0:
            $message = "Ocorreu um erro! ";
            $class = "alert-warning";
            break;
        case 1:
            $message = "Password Incorreta!";
            $class = "alert-warning";
            break;
        case 2:
            $message = "O username já está a ser utilizado!";
            $class = "alert-warning";
            break;
        case 3:
            $message = "Alteração efectuada com sucesso!";
            $class = "alert-success";
            break;
        case 7:
            $message="Já tinhas avaliado este Filme. Seu malandro.. andas a contornar o meu código. Pensavas que eu não estava à espera. PFFF..";

        default:
            $msg_show = false;

    }

    if ($msg_show) {
        echo "
<div class=\"alert $class alert-dismissible fade show\" role=\"alert\">" . $message . "
<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
    <span aria-hidden=\"true\">&times;</span>
</button>
</div>";
    }
}