<div id="fakebody" style="height: 120vh">
    <div class="main">

        <div class="containerS container">
            <div class="signup-content mt-5">
                <div class="col-lg-12 mx-auto">
                    <?php
                    if (isset($_GET["msg"])) {
                        $msg_show = true;
                        switch ($_GET["msg"]) {
                            case 0:
                                $message = "Ocorreu um erro no registo";
                                $class = "alert-warning";
                                break;
                            case 4:
                                $message = "Username já existente";
                                $class = "alert-warning";
                                break;
                            case 5:
                                $message = "Email já existente";
                                $class = "alert-warning";
                                break;
                            case 6:
                                $message = "Passwords não combinam";
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

                    <form method="POST" id="signup-form" class="signup-form" action="scripts/sc_signup.php">
                        <h2>Registo</h2>

                        <div class="form-group">
                            <input type="text" class="form-input" name="username" id="name" required="required" placeholder="Username"/>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-input" name="email" id="email" required="required" placeholder="Email"/>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-input" name="password" id="password" required="required"
                                   placeholder="Password"/>
                            <span toggle="#password" class="field-icon toggle-password"></span>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-input" name="password_confirmation" id="confirmation" required="required"
                                   placeholder="Confirma a Password"/>
                        </div>
                        <div class="form-group">
                            <a href="login.php" class="submit-link submit mr-3">Log in</a>
                            <input type="submit" name="submit" id="submit" class="form-submit submit" value="Registar"/>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
