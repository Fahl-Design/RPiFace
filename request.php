<?php
include_once('inc/Lights.php');

$value = htmlentities($_POST['light']);

if (!empty($value)) {

    $light = new lights();

    $lamp = $light->getById($value);
    //$light->switchState($lamp);

    $return = [
        'light' => $value,
        'state' => str_replace("\n", '', $light->switchState($lamp))
    ];

    echo json_encode($return);
}
?>
