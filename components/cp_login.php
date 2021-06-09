<div id="fakebody" style="height: 120vh">
    <div class="main">

        <div class="containerS container">
            <div class="signup-content mt-5">
                <?php
                if (isset($_GET["msg"])) {
                    $msg_show = true;
                    switch ($_GET["msg"]) {
                        case 1:
                            $message = "registo efectuado com sucesso";
                            $class = "alert-success";
                            break;
                        case 2:
                            $message = "O username, a password ou ambos estão errados.";
                            $class = "alert-warning";
                            break;
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
                ?>

                <form method="POST" id="signup-form" class="signup-form" action="scripts/sc_login.php">
                    <h2>Log In</h2>

                    <div class="form-group">
                        <input type="text" class="form-input" name="username" id="name" placeholder="Username"/>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-input" name="password" id="password" placeholder="Password"/>
                        <span toggle="#password" class="field-icon toggle-password"></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" id="submit" class="form-submit submit" value="Entrar"/>
                        <a href="signup.php" class="submit-link submit">Registo</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
