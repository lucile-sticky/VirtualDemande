<!DOCTYPE html>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/VirtualDemande/model/DAL/UtilisateurDAL.php');
session_start();
$user = null;
$pseudo = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'passwd', FILTER_SANITIZE_STRING);

//User connexion
if ($pseudo !== null && $password !== null) {
    $user = UtilisateurDAL::connection($pseudo, $password);
    if ($user) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['role_id'] = $user->getRole()->getId();
        $_SESSION['name'] = $user->getNom();
    } else {
        $_SESSION['user'] = false;
    }
}

$accountNumber = UtilisateurDAL::GetNumberAvailableUsers();
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Le titre</title>
        <!--jQuery library-->
        <script src="./view/library/jQuery/jquery-2.1.4.min.js"></script>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="./view/library/bootstrap/css/bootstrap-3.3.5.min.css">
        <!-- Optional theme -->
        <link rel="stylesheet" href="./view/library/bootstrap/css/bootstrap-theme-3.3.5.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="./view/library/bootstrap/js/bootstrap-js-3.3.5.min.js"></script>

        <!--Javascript file for all pages-->
        <script src="./view/javascript/main.js"></script>

        <!--Stylesheet for all pages-->
        <link rel="stylesheet" href="./view/css/main.css">

        <!-- <link rel="icon" type="image/png" href="./view/document/picture/favicon.png" />   -->
    </head>
    <body>


        <?php if (!(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['user_id'] !== false)): ?>
            <!-- Nav bar for unconnected user -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="?page=home">EVOLVE unplug</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                        <ul class="nav navbar-nav">
                            <li id="what_is_it"><a href="?page=what_is_it">What is it ? </a></li>
                            <li id="how_does_it_work"><a href="?page=how_does_it_work">How does it work ?</a></li>
                            <li id="register"><a href="?page=register">Register</a></li>
                        </ul>

                        <!--A implémenter correctement-->
                        <p class="navbar-text">Il reste <?php echo $accountNumber; ?> comptes disponibles</p>    

                        <ul class="nav navbar-nav navbar-right">
                            <li>
                                <form class="navbar-form" action="index.php" method="post">
                                    <div class="form-group">
                                        <input type="text" class="form-control"name="login" placeholder="Username">
                                        <input type="password" class="form-control" name="passwd" placeholder="Password">
                                    </div>
                                    <button type="submit" class="btn btn-default">Sign In</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        <?php else : ?>

            <!-- Nav bar for connected user -->
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="?page=home">EVOLVE</a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                        <ul class="nav navbar-nav">
                            <li id="what_is_it"><a href="?page=what_is_it">What is it ? <span class="sr-only">(current)</span></a></li>
                            <li id="how_does_it_work"><a href="?page=how_does_it_work">How does it work ?</a></li>
                        </ul>

                        <ul class="nav navbar-nav navbar-right">
                            <li id="what_is_it"><a href="?page=dashboard">Dashboard</a></li>
                            <li id="how_does_it_work"><a target="_blank" href="http://web-server:8080/guacamole-0.9.9/#/">Connect to your containers</a></li>  <!--ouvrir dans un nouvel onglet-->
                            <li>
                                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="?page=manage_containers">Containers</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="?page=manage_groups">Groups </a></li>
                                </ul>
                            </li>
                            <!--Items for the administrator-->
                            <?php if ($_SESSION["role_id"]== 3) : ?>
                            <li id="forms_administration"><a href="?page=forms_administration">Forms Administration</a></li>
                            <li id="logs"><a href="?page=logs">Logs</a></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION["name"]; ?> <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="?page=profile">Profile</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li> <form action="./controller/pages/Logout.php" method="post">
                                            <button class="dropdown-item dropdown-signout" type="submit" > Sign out </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        <?php endif; ?>



        <!-- Page to show -->
        <div id="content" class="col-lg-12">
            <?php
            $page_to_require = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_URL);
            if ($page_to_require !== null) {
                require_once './view/phtml/' . $page_to_require . '.php';
            } else {
                require_once './view/phtml/home.php';
            }
            ?>
            <span id="called_page" class="hidden"><?= $page_to_require ?></span>
        </div>
    </body>
</html>


