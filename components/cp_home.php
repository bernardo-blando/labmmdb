<!-- Page Content -->
<div class="container">

    <!-- Jumbotron Header -->
    <header class="jumbotron my-4"
            style=" background-image: url('images/panoramic.jpg');
    background-repeat: no-repeat;
    background-size: cover;

    -moz-background-size: cover;
    -webkit-background-size: cover;
    -o-background-size: cover;
    -ms-background-size: cover;
    background-position: center center;
color: white;
text-align: right">
        <h1 class="display-3">BEM VINDO!</h1>
        <p class="lead">Qual é o teu proximo filme?</p>
    </header>

    <h1 class="font-weight-bold">
        Mais bem Classificados
        <hr style="border: 1px solid black">
    </h1>

    <?php
    require_once "connections/connection.php";
    $link = new_db_connection();
    $stmt1 = mysqli_stmt_init($link);
    $query1 = "SELECT idFilms, ano, small_description, capa, genero, name_participant, idParticipants
                FROM mp_Films
                INNER JOIN mp_generos ON mp_Films.ref_idGeneros = mp_Generos.idGeneros
                INNER JOIN mp_participant_has_films ON mp_participant_has_films.ref_idFilms = mp_films.idFilms
                INNER JOIN mp_participants ON mp_participants.idParticipants=mp_participant_has_films.ref_idParticipants
                WHERE mp_participant_has_films.ref_idRoles=1
                ORDER BY avg_rating DESC LIMIT 4";

    if (mysqli_stmt_prepare($stmt1, $query1)) {
        mysqli_stmt_execute($stmt1); // Execute the prepared statement

        mysqli_stmt_bind_result($stmt1, $id_film, $ano, $small_description, $capa, $genero, $name, $id_participant); // Bind results


        echo '<div class="row text-center">';

        while (mysqli_stmt_fetch($stmt1)) {

            echo '<div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <a href="film.php?id='.$id_film.'"><img class="card-img-top " style="max-height: 355px;" src="images/capas/' . $capa . '"></a>
                <div class="card-body">
                    
                    <p class="card-subtitle" style="font-size=13px;"><b>' . $genero . ' (' . $ano . ')</b></p>
                    <p class="card-text" style="font-size: 14px">' . $small_description . '</p>
                    <p class="card-text" style="font-size: 13px"> Realizado por: <a href="participant.php?id=' . $id_participant . '"> ' . $name . '</a></p>
                </div>
                <div class="card-footer">
                    <a href="film.php?id=' . $id_film . '" class="btn btn-warning">Descobre mais</a>
                </div>
            </div>
        </div>';
        }

        echo '</div>';


        mysqli_stmt_close($stmt1); // Close statement

    } else {
        echo "error description: " . mysqli_error($link);


    }




    echo '
    
    
    <h1 class="font-weight-bold">
        Ultima estreias
        <hr style="border: 1px solid black">
    </h1>';


    $stmt = mysqli_stmt_init($link);
    $query = "SELECT idFilms, ano, small_description, capa, genero, name_participant, idParticipants 
                FROM mp_Films
                INNER JOIN mp_generos ON mp_Films.ref_idGeneros = mp_Generos.idGeneros
                INNER JOIN mp_participant_has_films ON mp_participant_has_films.ref_idFilms = mp_films.idFilms
                INNER JOIN mp_participants ON mp_participants.idParticipants=mp_participant_has_films.ref_idParticipants
                WHERE mp_participant_has_films.ref_idRoles=1
                ORDER BY ano DESC LIMIT 4;";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_execute($stmt); // Execute the prepared statement

        mysqli_stmt_bind_result($stmt, $id_film, $ano, $small_description, $capa, $genero, $name, $id_participant); // Bind results


        echo '<div class="row text-center">';

        while (mysqli_stmt_fetch($stmt)) {

            echo '<div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <a href="film.php?id='.$id_film.'"><img class="card-img-top " style="max-height: 355px;" src="images/capas/' . $capa . '"></a>
                <div class="card-body">
                    
                    <p class="card-subtitle" style="font-size=13px;"><b>' . $genero . ' (' . $ano . ')</b></p>
                    <p class="card-text" style="font-size: 14px">' . $small_description . '</p>
                    <p class="card-text" style="font-size: 13px"> Realizado por: <a href="participant.php?id=' . $id_participant . '"> ' . $name . '</a></p>
                </div>
                <div class="card-footer">
                    <a href="film.php?id=' . $id_film . '" class="btn btn-warning">Descobre mais</a>
                </div>
            </div>
        </div>';
        }

        echo '</div>';


        mysqli_stmt_close($stmt); // Close statement

    } else {
        echo "error description: " . mysqli_error($link);


    }
    mysqli_close($link);

    ?>


</div>

