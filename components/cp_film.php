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

if (isset($_GET["id"])) {
    require_once "connections/connection.php";
    $id_film = $_GET["id"];
    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);
    $query = "SELECT ano, description, capa, genero, poster, title, idFilms, name_participant, idParticipants
            FROM mp_Films
            INNER JOIN mp_generos ON mp_Films.ref_idGeneros = mp_Generos.idGeneros
            INNER JOIN mp_participant_has_films ON mp_participant_has_films.ref_idFilms = mp_films.idFilms
            INNER JOIN mp_participants ON mp_participants.idParticipants=mp_participant_has_films.ref_idParticipants
            WHERE mp_Films.idFilms = ? AND mp_participant_has_films.ref_idROles = 1";      //ROLE 1-REALIZADOR

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_film);

        mysqli_stmt_bind_result($stmt, $ano, $description, $capa, $genero, $poster, $title, $id_film_bd, $realizador, $id_realizador); // Bind results
        mysqli_stmt_execute($stmt); // Execute the prepared statement
        if (!mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);

            header("location: home.php?feed=0");
        } else {
            mysqli_stmt_close($stmt);


            #CONTENT----------------------------------------------------------CAPA----------------------------------------------------------------------------------------

            echo '
                <div class="container">
                
                    <div class="row">
                        <div class="col-lg-3  mt-4">
                            <img src="images/capas/' . $capa . '" style="width: 100%;">
                            <hr style=" border: 1px solid grey">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-3">Elenco</h5>';
            #----------------------------------------------------------------ELENCCO------------------------------------------------------------------

            $stmt1 = mysqli_stmt_init($link);
            $query1 = "SELECT image, name_participant
                        FROM mp_Participants
                        INNER JOIN mp_participant_has_films ON mp_Participants.idParticipants = mp_participant_has_films.ref_idParticipants
                        WHERE ref_idFilms = '$id_film_bd' AND ref_idRoles IN (2,3)
                        ORDER BY ref_idRoles";                                                  //PRIMEIRO OS ATORES PRINCIPAIS DEPOIS OS SECUNDARIOS

            if (mysqli_stmt_prepare($stmt1, $query1)) {

                mysqli_stmt_bind_result($stmt1, $actor_image, $actor_name); // Bind results
                mysqli_stmt_execute($stmt1); // Execute the prepared statement
                while (mysqli_stmt_fetch($stmt1)) {
                    echo '<p><span class="m-1" ><img src = "images/participants/' . $actor_image . '" ></span >' . $actor_name . '</p>';

                }
                mysqli_stmt_close($stmt1);

            } else {
                echo "Erro: " . mysqli_error($link);
                mysqli_stmt_close($stmt1);
            }


            echo '</div>      
                        </div>
                     </div> 
                   
                     
           <!---------------------------------------------------------------------INFO--------------------------------------------------------------------------------------------------> 
                        <div class="col-lg-9">
                
                            <div class="card my-4">
                                <img class="card-img-top img-fluid" style="max-height: 400px" src="images/posters/' . $poster . '">
                                <div class="card-body">
                                    <h3 class="card-title">' . $title . '<span style="font-size: 2vh; font-weight: normal">  (' . $ano . ') Realizado por <a style="font-size: 2.2vh" href="participant.php?id=' . $id_realizador . '">' . $realizador . '</a></span></h3>
                                    <h5>' . $genero . '</h5>
                                    <p class="card-text">' . $description . '</p>
                                <div>';


            #------------------------------------------RATING----------------------------------

            $stmt2 = mysqli_stmt_init($link);
            $query2 = "SELECT avg(Rating)
            FROM mp_ratings
            WHERE ref_idFilms = '$id_film_bd' ";

            if (mysqli_stmt_prepare($stmt2, $query2)) {

                mysqli_stmt_bind_result($stmt2, $avgRating); // Bind results
                mysqli_stmt_execute($stmt2); // Execute the prepared statement
                if (!mysqli_stmt_fetch($stmt2)) {

                    mysqli_stmt_close($stmt2);
                } else {

                    echo "<span id='estrelas' class='text-warning my-auto'>";                             //ESTRELAS CHEIAS OU VAZIAS DEPENDENDO DO RATING
                    for ($i = 1; $i < 6; $i++) {
                        if ($i <= round($avgRating)) {
                            echo "&#9733";
                        } else {
                            echo "&#9734";
                        }
                    }

                    mysqli_stmt_close($stmt2);

                    echo " <span style='color:black;'>" . round($avgRating, 1) . " stars</span></span>";

                    $_SESSION["avg_rating"] = round($avgRating, 3);

                    #-------------------------------------------------------BOTAO RATING-----------------------------------------------------------
                    if (isset($_SESSION["id_user_bb"])) {
                        $stmt2 = mysqli_stmt_init($link);
                        $query2 = "SELECT rating
                                    FROM mp_ratings
                                    WHERE ref_idFilms = ? AND ref_idUsers= ? ";

                        if (mysqli_stmt_prepare($stmt2, $query2)) {
                            mysqli_stmt_bind_param($stmt2, 'ii', $id_film_bd, $_SESSION["id_user_bb"]); // Bind params
                            mysqli_stmt_bind_result($stmt2, $rating_user); // Bind results
                            mysqli_stmt_execute($stmt2); // Execute the prepared statement
                            if (!mysqli_stmt_fetch($stmt2)) {                                                       //SE AINDA NAO DEU RATE AO FILME
                                echo "<button type='button' data-target='#myModal' data-toggle='modal' style='margin-left:68%;' class='btn-warning btn'>Avalia o filme</button>";
                                mysqli_stmt_close($stmt2);
                            } else {                                                                                 //SE JA DEU RATE AO FILME
                                echo "<button type='button' data-target='#myModal2' data-toggle='modal' style='margin-left:64%;' class='btn-warning btn'>Alterar Avaliação</button>";

                                mysqli_stmt_close($stmt2);
                            }
                        } else
                            echo "erro: " . mysqli_error($link);


                    } else {
                        echo "<button type='button' data-target='#myModal' data-toggle='modal' style='margin-left:68%;' class='btn-warning btn'>Avalia o filme</button>";
                    }
                }
            } else {
                echo "erro: " . mysqli_error($link);
            }                                                          //-----------------------ESTRELAS----------------------------


            $stmt4 = mysqli_stmt_init($link);
            $query4 = "SELECT ref_idComments
                            FROM mp_upvotes
                            WHERE ref_idUsers=? ";

            if (mysqli_stmt_prepare($stmt4, $query4)) {

                mysqli_stmt_bind_param($stmt4, "i", $_SESSION["id_user_bb"]);
                mysqli_stmt_bind_result($stmt4, $upvote_id_comment);
                mysqli_stmt_execute($stmt4);
                $upvote_array = array();
                while (mysqli_stmt_fetch($stmt4)) {
                    array_push($upvote_array, $upvote_id_comment);

                }


            } else {

                echo "<p></p>erro :" . mysqli_error($link);
            }
            mysqli_stmt_close($stmt4);

            $stmt5 = mysqli_stmt_init($link);
            $query5 = "SELECT ref_idComments
                            FROM mp_downvotes
                            WHERE ref_idUsers=? ";

            if (mysqli_stmt_prepare($stmt5, $query5)) {

                mysqli_stmt_bind_param($stmt5, "i", $_SESSION["id_user_bb"]);
                mysqli_stmt_bind_result($stmt5, $downvote_id_comment);
                mysqli_stmt_execute($stmt5);
                $downvote_array = array();
                while (mysqli_stmt_fetch($stmt5)) {
                    array_push($downvote_array, $downvote_id_comment);

                }


            } else {

                echo "<p></p>erro :" . mysqli_error($link);
            }

            mysqli_stmt_close($stmt5);

            echo '              </div>                  
                            </div>
                           </div>
                            <!-- /.card -->
                            
                            
                            
                            
                            
                            

<!-------------------------------------------------------------------------REVIEWS--------------------------------------------------------------------------------------->
           
                         <style> 
                         i{
                         width: 10px;
                         color: black;
                         }
                         </style>   
                           <div class="card card-outline-secondary my-4 container">
                                <div class="card-header  px-0 py-3">
                                   <h5> Reviews</h5>           
                                </div>
                           <section class="card-body row">';

$offset=($page*4);
            $stmt3 = mysqli_stmt_init($link);
            $query3 = "SELECT comment, username, date_comment, comment_score, idComments                  
                        FROM mp_comments
                        INNER JOIN mp_users ON mp_comments.ref_idUsers = mp_users.idUsers
                        WHERE ref_idFilms = ?
                        ORDER BY comment_score DESC
                        LIMIT ?,4
                       ";

            if (mysqli_stmt_prepare($stmt3, $query3)) {
                mysqli_stmt_bind_param($stmt3, 'ii', $id_film_bd, $offset); // Bind params
                mysqli_stmt_bind_result($stmt3, $comment, $user_comment, $date_comment, $comment_score, $comment_id); // Bind results
                mysqli_stmt_execute($stmt3); // Execute the prepared statement
                $array_de_aluno_brilhante = array(                                                       //ARRAY PARA PODER ABRIR A MODAL CERTA DE EDIÇAO DE COMMENT
                    array(),
                    array()
                );

                while (mysqli_stmt_fetch($stmt3)) {
                    if (isset($_SESSION["username_bb"])) {                                                 //CASO TENHA SESSÂO
                        if ($user_comment == $_SESSION["username_bb"]) {                                   //CASO SEJA O SEU COMMENT
                            array_push($array_de_aluno_brilhante[0], $comment);
                            array_push($array_de_aluno_brilhante[1], $comment_id);
                            echo '                            
                            <div class="col-10 p-0 "><p class="mb-0 pb-1">' . $comment . '</p >
                                    <small class="text-muted" > Posted by <b class="text-primary">' . $user_comment . '</b> on <b class="text-primary">' . $date_comment . '</b> </small > <i data-target=\'#myModal3' . $comment_id . '\' data-toggle=\'modal\' class="fas m-2 fa-edit"></i> <a href="scripts/sc_delete_comment.php?film=' . $id_film_bd . '&id=' . $comment_id . '"><i class="fas m-2 fa-trash-alt"></i></a>
                            </div>
                                  <hr class="col-11 ml-0 mt-2 mb-5" style="border: 1px solid grey" >';
                        } else {

                            //CASO NAO SEJA O SEU COMMENT

                            foreach ($upvote_array as $item) {
                                if ($item == $comment_id) {
                                    echo "<style>
                                                  #uparrow" . $comment_id . "{
                                                  color: #0069d9!important;
                                                  }
                                               </style>";
                                }
                            }
                            foreach ($downvote_array as $item2) {
                                if ($item2 == $comment_id) {
                                    echo "<style>
                                                  #downarrow" . $comment_id . "{
                                                  color: #0069d9!important;
                                                  }
                                              </style>";
                                }
                            }


                            echo '<div class="col-10 p-0"><p class="mb-0 pb-1">' . $comment . '</p >
                                    <small class="text-muted" > Posted by <b class="text-primary">' . $user_comment . '</b> on <b class="text-primary"> ' . $date_comment . '</b> </small > 
                                  </div>
                                  
                                  <div class="col-1 text-center " style="width: 20px; align-content: center; display: inline;">';

                            echo '<a  href="scripts/sc_update_score.php?vote=1&id=' . $comment_id . '&film=' . $id_film_bd . '"><i id="uparrow' . $comment_id . '"  class="fas  fa-arrow-up m-auto text-center m-auto " style="width:100%;"></i></a>
                                      <p class="text-center my-auto">' . $comment_score . '</p>
                                      <a  href="scripts/sc_update_score.php?vote=-1&id=' . $comment_id . '&film=' . $id_film_bd . '"><i id="downarrow' . $comment_id . '"  class="fas fa-arrow-down  text-center m-auto" style="width:100%;"></i></a>
                                  </div>
                                  <hr class="col-11 ml-0 mt-2 mb-5" style="border: 1px solid grey" >';
                        }

                    }else {                                                                                //CASO N TENHA SESSÂO
                            echo '<div class="col-10 p-0"><p class="mb-0 pb-1">' . $comment . '</p >
                                    <small class="text-muted" > Posted by <b class="text-primary">' . $user_comment . '</b> on <b class="text-primary"> ' . $date_comment . '</b></small >
                              </div>
                                    <hr class="col-11 ml-0 mt-2 mb-5" style="border: 1px solid grey!important;" >';
                        }
                    }
                    mysqli_stmt_close($stmt3);
                }
            else {
                    echo "erro: " . mysqli_error($link);
                }



                echo '
                                    <a data-target="#myModal4" data-toggle=\'modal\'class="btn btn-warning mr-5">Leave a Review</a>';

            $stmt10 = mysqli_stmt_init($link);
            $query10 = "SELECT COUNT(idComments)
            FROM mp_comments
            WHERE ref_idFilms = ?";
            if (mysqli_stmt_prepare($stmt10, $query10)) {
                mysqli_stmt_bind_param($stmt10, "i", $id_active);

                mysqli_stmt_execute($stmt10);
                mysqli_stmt_bind_result($stmt10, $count_comments);
                mysqli_stmt_fetch($stmt10);
                mysqli_stmt_close($stmt10);

            } else {
                echo mysqli_error($link);
            };
            $Npage = ceil($count_comments / 4);

            if ($page != 0) {
                echo '<a   href="film.php?id=' . $id_active . '&page=' . ($page - 1) . '">Previows page <<<      </a>';
            };
            echo '<p > PAGE ' . ($page + 1) . '/' . ($Npage) . '</p>';
            if (($page + 1) < ($Npage)) {
                echo '<a   href="film.php?id=' . $id_active . '&page=' . ($page + 1) . '">        >>> Next page</a>';
            }


                    echo '           
                                </section >
                            </div >
                      </div ><!-- /.col-lg-9-->
                  </div>
            
              ';


            }
        }
    else {
            echo "Erro: " . mysqli_error($link);

        }
    } else {
        header("location: home.php?feed=0");
    }
    mysqli_close($link);


#---------------------------------------------------------------MODAL 1-------------------------------------------------------------->
    echo '
<div class="modal fade" id = "myModal" role = "dialog" >

    <div class="modal-dialog modal-sm" >
      <div class="modal-content" >
        <div class="modal-body m-auto" style = "text-align: center;" id = "rate" > ';

    if (isset($_SESSION["username_bb"])) {
        echo '<style >
        .estrelinha{
                        color:#f0ad4e;
                    }  
</style >
          <span  class="text-warning" style = "font-size:4vh; " > <a id = "e1" class="estrelinha" href = "scripts/sc_rating.php?rate=1&id=' . $id_film_bd . '" >&#9734</a> <a id="e2" class="estrelinha" href="scripts/sc_rating.php?rate=2&id=' . $id_film_bd . '">&#9734</a> <a id="e3" class="estrelinha" href="scripts/sc_rating.php?rate=3&id=' . $id_film_bd . '">&#9734</a> <a id="e4" class="estrelinha" href="scripts/sc_rating.php?rate=4&id=' . $id_film_bd . '">&#9734</a> <a id="e5" class="estrelinha"  href="scripts/sc_rating.php?rate=5&id=' . $id_film_bd . '">&#9734</a> </span>';
    } else {
        echo '<p>Faça Login para poder fazer avaliações. <a href="login.php">Clique Aqui</a></p>';

    }
    echo '    </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
        </div>
      </div>
    </div>
  </div>







<!---------------------------------------------------------------MODAL 2-------------------------------------------------------------->
<div class="modal fade" id = "myModal2" role = "dialog" >

    <div class="modal-dialog modal-sm" >
      <div class="modal-content" >
        <div class="modal-body m-auto" style = "text-align: center; font-size: 4vh; ">';


    for ($i = 1; $i < 6; $i++) {
        if ($i <= round($rating_user)) {
            echo "<a class='text-warning estrelinhas'  href='scripts/sc_rating_update.php?rate=" . $i . "&id=" . $id_film_bd . "'>&#9733</a>";
        } else {
            echo "<a class='text-warning estrelinhas' href='scripts/sc_rating_update.php?rate=" . $i . "&id=" . $id_film_bd . "'>&#9734</a>";
        }
    }
    echo '
</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
        </div>
      </div>
    </div>
  </div>


<!---------------------------------------------------------------MODAL 4 -------------------------------------------------------------->

<div class="modal fade" id = "myModal4" role = "dialog" >
    <div class="modal-dialog modal-sm" >
      <div class="modal-content" >
        <div class="modal-body m-auto" style = "text-align: center; font-size: 2vh; ">';


         if (isset($_SESSION["username_bb"])) {
       echo '<form method="post" action="scripts/sc_add_comment.php?id=' . $id_film_bd . '" >
            <input aria-rowcount="3" class="m-auto d-block" style="width:90%; height:4vh!important;" type="text" name="comment" placeholder="Escreve a tua review!">';


    } else {
        echo '<p>Faça Login para poder escrever reviews. <a href="login.php">Clique Aqui</a></p>';

    }

        echo '
        </div>
            <div class="modal-footer">
        
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
                if (isset($_SESSION["username_bb"])) {
                echo '<button type="submit" class="btn btn-warning"  >Submeter</button></form>';};

                echo '
             </div>
         </div>
       </div>
    </div>
</div>
<!-------------------------------------------------------------------------------------------------------------------------------------------------->';

    for ($i = 0; $i < count($array_de_aluno_brilhante[0]); $i++) {
        echo '<!---------------------------------------------------------------MODAL 3.x -------------------------------------------------------------->
                            
                            <div class="modal fade" id = "myModal3' . $array_de_aluno_brilhante[1][$i] . '" role = "dialog" >
                              <div class="modal-dialog modal-sm" >
                                <div class="modal-content" >
                                  <div class="modal-body m-auto" style = "text-align: center; font-size: 4vh; "></div>
                                    <form method="post" action="scripts/sc_update_comment.php?id=' . $array_de_aluno_brilhante[1][$i] . '&film=' . $id_film_bd . '" >
                                    <input class="m-auto d-block" style="width:90%; height:8vh!important;" type="text" name="comment" value="' . $array_de_aluno_brilhante[0][$i] . '">
                                    <div class="modal-footer">
                                    
                                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
                                      <button type="submit" class="btn btn-warning" >Submeter</button></form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <!-------------------------------------------------------------------------------------------------------------------------------------------------->';
    }




