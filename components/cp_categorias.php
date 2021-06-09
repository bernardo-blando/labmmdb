<!-- Page Content -->
<?php
if (isset($_GET["id"])) {
    $id_active = $_GET["id"];
} else {
    $id_active = 0;
}

if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 0;
}


echo '
<div class="container" style="min-height: 90vh">

    <div class="row">

        <div class="col-lg-3">

            <h1  class="my-4">Categorias</h1>

            <div style="position: fixed; width: 18vw" class="list-group">';

require_once "connections/connection.php";
$link = new_db_connection();
$stmt = mysqli_stmt_init($link);
$query = "SELECT idGeneros, genero
            FROM mp_generos";
if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_genero, $genero);

    while (mysqli_stmt_fetch($stmt)) {
        echo '<a href="categorias.php?id=' . $id_genero . '"  class="list-group-item text-dark';
        if ($id_genero == $id_active) {
            echo ' bg-warning font-weight-bold';
        }
        echo '">' . $genero . '</a>';

    };

    mysqli_stmt_close($stmt);

} else {
    echo mysqli_error($link);
}
echo '
<form method="get" class="text-center mt-3" action="categorias.php" >
<input  type="text" class="list-group-item d-inline text-dark" name="search" placeholder="Procurar por título ">
<button type="submit" class="btn mt-2 btn-primary"><i class="fas fa-search"></i> Procurar</button>

</form>';
?>


</div>
</div>
<!-- /.col-lg-3 -->

<div class="col-lg-9">

    <!--<div id="carouselExampleIndicators" class="carousel slide my-4" data-ride="carousel">
          <ol class="carousel-indicators">
              <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
              <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
              <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner" role="listbox">
              <div class="carousel-item active">
                  <img class="d-block img-fluid" src="http://placehold.it/900x350" alt="First slide">
              </div>
              <div class="carousel-item">
                  <img class="d-block img-fluid" src="http://placehold.it/900x350" alt="Second slide">
              </div>
              <div class="carousel-item">
                  <img class="d-block img-fluid" src="http://placehold.it/900x350" alt="Third slide">
              </div>
          </div>
          <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
          </a>
      </div>-->


    <div class="row">

<?php


if (isset($_GET["id"])) {
    $query1 = "SELECT idFilms, ano, small_description, capa,  name_participant, title, avg_rating
                FROM mp_Films
                INNER JOIN mp_generos ON mp_Films.ref_idGeneros = mp_Generos.idGeneros
                INNER JOIN mp_participant_has_films ON mp_participant_has_films.ref_idFilms = mp_films.idFilms
                INNER JOIN mp_participants ON mp_participants.idParticipants=mp_participant_has_films.ref_idParticipants
                WHERE mp_participant_has_films.ref_idRoles=1 AND idGeneros= ". $id_active . "
               /* ORDER BY avg_rating DESC
                LIMIT ?,6*/";
    $param = ($page * 6);
} elseif (isset($_GET["search"])) {

    $query1 = "SELECT idFilms, ano, small_description, capa,  name_participant, title, avg_rating
                FROM mp_Films
                INNER JOIN mp_generos ON mp_Films.ref_idGeneros = mp_Generos.idGeneros
                INNER JOIN mp_participant_has_films ON mp_participant_has_films.ref_idFilms = mp_films.idFilms
                INNER JOIN mp_participants ON mp_participants.idParticipants=mp_participant_has_films.ref_idParticipants
                WHERE mp_participant_has_films.ref_idRoles=1 AND title LIKE ?
               ORDER BY avg_rating DESC
                LIMIT 6;
            
                
                ";
    $param = "%" . $_GET["search"] . "%";
}
$stmt1 = mysqli_stmt_init($link);


if (mysqli_stmt_prepare($stmt1, $query1)) {
    if (isset($_GET["id"])) {
    //mysqli_stmt_bind_param($stmt1, 'ii', $id_active, $param);
      //  mysqli_stmt_bind_param($stmt1, 'i',  $param); //ataque mysqli
    }elseif (isset($_GET["search"])){
        mysqli_stmt_bind_param($stmt1, 's',$param);
    }
    mysqli_stmt_execute($stmt1); // Execute the prepared statement
    mysqli_stmt_bind_result($stmt1, $id_film, $ano, $small_description, $capa, $name, $title, $avg_rating); // Bind results

$i=0;
    while (mysqli_stmt_fetch($stmt1)) {
$i++;
        echo '<div class="col-lg-4 col-md-6 my-4">
                    <div class="card h-100">
                        <a href="film.php?id=' . $id_film . '"><img class="card-img-top" src="images/capas/' . $capa . '" alt=""></a>
                        <div class="card-body">
                            <h4 class="card-title">
                                <a href="film.php?id=' . $id_film . '">' . $title . '</a><small> (' . $ano . ')</small>
                            </h4>
                            <h5 style="font-size: 2vh;">Realizado por: <a href="#">' . $name . ' </a></h5>
                            <p class="card-text">' . $small_description . '</p>
                        </div>
                        <div class="card-footer">';

        echo '<small class="text-warning" >';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= round($avg_rating)) {
                echo '&#9733;';
            } else {
                echo '&#9734;';
            };
        }
        echo ' </small>' . $avg_rating . '/5</div >
                    </div >
        </div>';

    };
if ($i==0){
    echo '<h4 class="text-center m-5">A procura não retornou resultados</h4>';
}

    mysqli_stmt_close($stmt1); // Close statement

} else {
    echo "error description: " . mysqli_error($link);


};

//---------------------------------------------------------------------CHANGE PAGE------------------------------------------------
echo '<div class=" col-12 text-center" style="font-size: 2vh;">
        ';


$stmt2 = mysqli_stmt_init($link);
$query2 = "SELECT COUNT(idFilms)
            FROM mp_films
            WHERE ref_idGeneros = ?";
if (mysqli_stmt_prepare($stmt2, $query2)) {
    mysqli_stmt_bind_param($stmt2, "i", $id_active);

    mysqli_stmt_execute($stmt2);
    mysqli_stmt_bind_result($stmt2, $count);
    mysqli_stmt_fetch($stmt2);
    mysqli_stmt_close($stmt2);

} else {
    echo mysqli_error($link);
};
$Npage = ceil($count / 6);

if ($page != 0) {
    echo '<a class= m-auto"  href="categorias.php?id=' . $id_active . '&page=' . ($page - 1) . '">Previows page <<<      </a>';
};
echo '<p class="m-auto"> PAGE ' . ($page + 1) . '/' . ($Npage) . '</p>';
if (($page + 1) < ($Npage)) {
    echo '<a  class="m-auto" href="categorias.php?id=' . $id_active . '&page=' . ($page + 1) . '">        >>> Next page</a>';
}


echo '       
        </div>';
//-------------------------------------------------------------------------------------------------------------------------------

echo '


                </div>

               
            </div>
            <!-- /.row --> 




        </div >
        <!-- /.col - lg - 9-->

  </div >
    <!-- /.row-->

</div >
<!-- /.container-->';
mysqli_close($link);