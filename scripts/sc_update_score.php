<?php
session_start();
echo var_dump($_SESSION);
echo "<p>" . var_dump($_GET) . "</p>";

if (isset($_SESSION["username_bb"]) && isset($_SESSION["id_user_bb"]) && isset($_GET["vote"]) && isset($_GET["id"]) && isset($_GET["film"])) {

    $vote = round($_GET["vote"]);
    echo "<p>1</p>";
    if ($vote != 1 || $vote != -1) {
        echo "<p></p>2";
        require_once "../connections/connection.php";

        $id_user = $_SESSION["id_user_bb"];
        $comment_id = $_GET["id"];
        $film_id = $_GET["film"];

        $link = new_db_connection();


        $stmt = mysqli_stmt_init($link);  //-------------------------------------------------STATMENT 0-----------------------------------
        $query = "SELECT ref_idUsers
                  FROM mp_comments
                  WHERE idComments = ? AND ref_idFilms = ?";
//---------------------------------------VERIFICAR SE O COMENTARIO NÃO É DO UTILIZADOR QUE FEZ O RATE---------------
        if (mysqli_stmt_prepare($stmt, $query)) {
            echo "<p></p>3";
            mysqli_stmt_bind_result($stmt, $id_user_comment);
            mysqli_stmt_bind_param($stmt, "ii", $comment_id, $film_id);
            mysqli_stmt_execute($stmt);

            if (!mysqli_stmt_fetch($stmt)) {

                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header("location:../film.php?id=" . $film_id . "&feed=0");
            };

        } else {

            echo "<p></p>erro stmt0: " . mysqli_error($link);
        }

        mysqli_stmt_close($stmt);

        if ($id_user != $id_user_comment) {

            $stmt5 = mysqli_stmt_init($link);  //-------------------------------------------------STATMENT 5-----------------------------------
            $query5 = "SELECT COUNT(ref_idUsers)
                  FROM mp_upvotes
                  WHERE ref_idComments = ? AND ref_idUsers =?";
            //---------------------------------------VERIFICAR  ALGUM RATE ANTERIOR ANTERIOR---------------
            if (mysqli_stmt_prepare($stmt5, $query5)) {
                echo "<p></p>3";

                mysqli_stmt_bind_result($stmt5, $old_upvote);
                mysqli_stmt_bind_param($stmt5, "ii", $comment_id, $id_user);
                mysqli_stmt_execute($stmt5);

                if (!mysqli_stmt_fetch($stmt5)) {
                    mysqli_stmt_close($stmt5);
                    mysqli_close($link);
                    header("location:../home.php?feed=0");

                };
                mysqli_stmt_close($stmt5);

            } else {

                echo "<p></p>erro stmt5: " . mysqli_error($link);
            }


            if ($old_upvote == 1) {
                $oldvote = 1;
            } else {

                $stmt6 = mysqli_stmt_init($link);  //-------------------------------------------------STATMENT 6-----------------------------------
                $query6 = "SELECT COUNT(ref_idUsers)
                  FROM mp_downvotes
                  WHERE ref_idComments = ? AND ref_idUsers =?";
                //---------------------------------------VERIFICAR  ALGUM RATE ANTERIOR ANTERIOR---------------
                if (mysqli_stmt_prepare($stmt6, $query6)) {
                    echo "<p></p>3";

                    mysqli_stmt_bind_result($stmt6, $old_downvote);
                    mysqli_stmt_bind_param($stmt6, "ii", $comment_id, $id_user);
                    mysqli_stmt_execute($stmt6);

                    if (!mysqli_stmt_fetch($stmt6)) {
                        mysqli_stmt_close($stmt6);
                        mysqli_close($link);
                        header("location:../home.php?feed=0");
                    };
                    mysqli_stmt_close($stmt6);

                } else {

                    echo "<p></p>erro stmt6: " . mysqli_error($link);
                }
                echo "old-downvote". $old_downvote;
                if ($old_downvote == 1) {
                    $oldvote = -1;
                } else {
                    $oldvote = 0;
                }


            }
echo $oldvote, $old_upvote, $old_downvote;

            echo "<p></p>3";
            if ($vote == -1 && $oldvote == 1) {

                $table = "mp_downvotes";
                $query4 = "DELETE FROM mp_upvotes
                            WHERE mp_upvotes.ref_idComments = ? AND mp_upvotes.ref_idUsers = ?";
                echo "$vote == -1 && $oldvote == 1";

            } elseif ($vote == 1 && $oldvote == -1) {

                echo "$vote == 1 && $oldvote == -1";
                $query4 = "DELETE FROM mp_downvotes
                            WHERE mp_downvotes.ref_idComments = ? AND mp_downvotes.ref_idUsers = ?";
                $table = "mp_upvotes";
                echo "<p></p>5";
            } elseif ($vote == -1 && $oldvote == -1) {
                echo "$vote == -1 && $oldvote == -1";
                $query4 = "DELETE FROM mp_downvotes
                            WHERE mp_downvotes.ref_idComments = ? AND mp_downvotes.ref_idUsers = ?";

            } elseif ($vote == 1 && $oldvote == 1) {
                echo "$vote == 1 && $oldvote == 1";
                $query4 = "DELETE FROM mp_upvotes
                            WHERE mp_upvotes.ref_idComments = ? AND mp_upvotes.ref_idUsers = ?";

            } elseif ($vote == -1 && $oldvote == 0) {
                echo "$vote == -1 && $oldvote == 0";

                $table = "mp_downvotes";

            } elseif ($vote == 1 && $oldvote == 0) {
                echo "$vote == 1 && $oldvote == 0";

                $table = "mp_upvotes";
            } else {
                mysqli_close($link);
                header("location:../home.php?feed=0");
            }

            if(isset($query4)) {
                //-----------------------SE FIZER UM UPVOTE, DELETE O DOWNVOTE E VICE-VERSA

                $stmt4 = mysqli_stmt_init($link); //  ----------------     STMT 4..........................................................

                if (mysqli_stmt_prepare($stmt4, $query4)) {
                    mysqli_stmt_bind_param($stmt4, "ii", $comment_id, $id_user);
                    mysqli_stmt_execute($stmt4);

                echo "<p>6-delete</p>";
                } else {
                    echo mysqli_error($link);
                }
                mysqli_stmt_close($stmt4);
            }

            if (isset($table)) {

                $stmt1 = mysqli_stmt_init($link); //--------------------------------------------------STATMENT 1------------------------------

                $query1 = "INSERT INTO " . $table . " (ref_idUsers, ref_idComments)
                VALUES (?,?)";

                if (mysqli_stmt_prepare($stmt1, $query1)) {
                    echo "<p></p>6- insert";
                    mysqli_stmt_bind_param($stmt1, "ii", $id_user, $comment_id);
                    mysqli_stmt_execute($stmt1);

                } else {

                    echo "<p></p>erro stmt1: " . mysqli_error($link);
                }

                mysqli_stmt_close($stmt1);
            }

            mysqli_close($link);

            $link = new_db_connection();
            $stmt2 = mysqli_stmt_init($link);            //--------------------------------------------------STATMENT 2------------------------------


            $query2 = "SELECT SUM((SELECT  COUNT(mp_upvotes.ref_idComments)as upvotes
                                                FROM mp_upvotes
                                                WHERE ref_idComments = ?)-(SELECT  COUNT(mp_downvotes.ref_idComments)as downvotes
                                                                           FROM mp_downvotes
                                                                          WHERE ref_idComments = ?))AS score";
            //-----------------------------------------SCORE = UPVOTES-DOWNVOTES-------------------------

            if (mysqli_stmt_prepare($stmt2, $query2)) {
                echo "<p></p>7";
                mysqli_stmt_bind_result($stmt2, $score);
                mysqli_stmt_bind_param($stmt2, "ii", $comment_id, $comment_id);
                mysqli_stmt_execute($stmt2);

                if (!mysqli_stmt_fetch($stmt2)) {

                    mysqli_close($link);
                    echo $score;
                    //header("location:../home.php?feed=0");
                };

            } else {

                echo "<p></p>erro stmt2: " . mysqli_error($link);
            }
            echo "<p></p>score=" . $score;
            mysqli_stmt_close($stmt2);


            $stmt3 = mysqli_stmt_init($link);            //--------------------------------------------------STATMENT 3------------------------------

            $query3 = "UPDATE mp_comments
                       SET comment_score =" . $score . " 
                       WHERE idComments = ?";
            //-----------------------------------------SCORE = UPVOTES-DOWNVOTES-------------------------

            if (mysqli_stmt_prepare($stmt3, $query3)) {
                echo "<p></p>8";
                mysqli_stmt_bind_param($stmt3, "i", $comment_id);
                mysqli_stmt_execute($stmt3);

            } else {

                echo "<p></p>erro stmt3: " . mysqli_error($link);
            }
            echo "<p></p>9";
            mysqli_stmt_close($stmt3);
            mysqli_close($link);
            header("location:../film.php?id=".$film_id);

        } else {
            echo "<p></p>10";
            mysqli_close($link);
            header("location:../home.php?feed=0");
        }

    }
} else {
    header("location:../home.php?feed=0");
}

