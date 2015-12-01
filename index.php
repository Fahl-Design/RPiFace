<?php
include('inc/Lights.php');
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
    <link rel="stylesheet" href="css/styles.css">
    <style>

        .centerVParent {
            position: relative;
        }

        .centerVChild {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .center {
            position: absolute;
            margin: 0 auto;
            min-width: 275px;
        }
    </style>

</head>
<body>

<!-- Simple header with scrollable tabs. -->
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header centerVParent">
    <div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect ">
        <div class="mdl-tabs__tab-bar">
            <a href="#starks-panel" class="mdl-tabs__tab is-active">Light</a>
            <a href="#lannisters-panel" class="mdl-tabs__tab">Temperature</a>
        </div>

        <div class="mdl-tabs__panel is-active centerVChild" id="starks-panel">
            <main class="mdl-layout__content  ">
                <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                    <thead>
                    <tr>
                        <th class="mdl-data-table__cell--non-numeric">Lamp</th>
                        <th class="mdl-data-table__cell--non-numeric">Status</th>
                        <th class="mdl-data-table__cell--non-numeric">Switch</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lights as $lightBall) : ?>
                        <tr>
                            <td class="mdl-data-table__cell--non-numeric">
                                <?php echo htmlentities($lightBall->name); ?>
                            </td>
                            <td class="mdl-data-table__cell--non-numeric status">
                                <?php echo ((bool)$lightBall->status) ? '<i class="material-icons">wb_incandescent</i>' : '<i class="material-icons">brightness_3</i>' ?>
                            </td>
                            <td class="mdl-data-table__cell--non-numeric">
                                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="switch-<?php echo $lightBall->id ?>">
                                    <input type="checkbox" id="switch-<?php echo $lightBall->id ?>" data-swtichid="<?php echo $lightBall->id ?>"
                                           class="mdl-switch__input" <?php echo ((bool)$lightBall->status) ? 'checked' : '' ?>>
                                    <span class="mdl-switch__label"></span>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach ?>

                    </tbody>
                </table>
            </main>
        </div>
        <div class="mdl-tabs__panel centerVChild" id="lannisters-panel">
            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp temperatureTable">
                <thead>
                <tr>
                    <th class="mdl-data-table__cell--non-numeric">
                        Show Log / Date
                        <button class="mdl-button mdl-js-button mdl-button--icon refreshTemp" >
                            <i class="material-icons">refresh</i>
                        </button>
                    </th>
                    <th>Temperatur</th>
                    <th>Luftfeuchtigkeit</th>
                </tr>
                </thead>
                <tbody class="roomTemp">
                <tr class="">
                    <td></td>
                    <td class="temp"></td>
                    <td class="humidity"></td>
                </tr>

                </tbody>
            </table>

        </div>
        <div>
            <div>
                <canvas id="canvas" style="width: 700%; min-height: 450px !important;"></canvas>
            </div>
        </div>
    </div>

</div>

<script src="js/material.min.js"></script>
<script src="js/jquery.js"></script>
<script src="js/request.js"></script>
<script src="js/chart.js"></script>
<script>

</script>
</body>
</html>
