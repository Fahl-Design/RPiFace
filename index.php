<?php
include('inc/Lights.php');
include('./vendor/autoload.php');
?>
<!doctype html>

<?php
/** @var lights $light */
$light = new lights();

$lights = $light->getLightObjects();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPiFace Light Control</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/android-desktop.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="images/ios-desktop.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

    <link rel="shortcut icon" href="images/favicon.png">

    <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
    <!--
    <link rel="canonical" href="http://www.example.com/">
    -->

    <link href='//fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en' rel='stylesheet'
          type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" href="css/material.min.css">
    <link rel="stylesheet" href="./vendor/twitter/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="page-header ">
                <h1>RPiFace
                    <small class="visible-lg-block visible-md-block visible-sm-block ">Mumbi 433Hz Lamp Control with Temperature History</small>
                </h1>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">&times;</span></button>
                    <h4 class="temperatureNow"></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Lamps</h3>
                        </div>
                        <div class="panel-body">
                            <?php foreach ($lights as $lightBall) : ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><?php echo htmlentities($lightBall->name); ?></h3>
                                        </div>
                                        <div class="panel-body statusPanel <?php echo ((bool)$lightBall->status) ? 'alert-warning' : '' ?>"
                                             data-swtichid="<?php echo $lightBall->id ?>"
                                             id="switch-<?php echo $lightBall->id ?>">
                                            <p class="status alert text-center">
                                                <?php echo ((bool)$lightBall->status) ? '<i class="material-icons">wb_incandescent</i>' : '<i class="material-icons">brightness_3</i>' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default visible-md visible-sm visible-lg">
                <div class="panel-heading">
                    <h3 class="panel-title ">
                        Temperatures
                        <button class="mdl-button mdl-js-button mdl-button--icon refreshTemp">
                            <i class="material-icons">refresh</i>
                        </button>
                    </h3>
                </div>
                <div class="panel-body">
                    <canvas id="canvas" class="img-responsive"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery.js"></script>
<script src="./vendor/twitter/bootstrap/dist/js/bootstrap.js"></script>
<script src="js/material.min.js"></script>

<script src="js/request.js"></script>
<script src="js/chart.js"></script>
</body>
</html>
