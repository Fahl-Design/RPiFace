<?php
include_once('Light.php');

$valueLight = htmlentities($_POST['light']);
$valueTemp = htmlentities($_POST['temp']);
$valueNow = htmlentities($_POST['tempNow']);

$force = htmlentities($_POST['force']);

if (!empty($force)) {
    $light = new light();
    $rs = [];
    if ($force === 'ON') {
        $rs = $light->allOn();
    } else if ($force === 'OFF') {
        $rs = $light->allOff();
    }

    echo json_encode($rs);
}

if (!empty($valueLight)) {
    $light = new light();
    $return = [];

    if ($valueLight !== 'all') {
        $lamp = $light->getById($valueLight);
        $return[] = [
            'light' => $valueLight,
            'state' => str_replace("\n", '', $lamp->switchState())
        ];
    }

    echo json_encode($return);
}
if (!empty($valueTemp)) {

    $row = 1;
    if (($handle = fopen("templog.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;
            $rs[] = $data;
        }
        fclose($handle);
    }
    echo json_encode(array_slice($rs, -50, 50, true));
    //echo json_encode($rs);
}
if (!empty($valueNow)) {
    if (($handle = fopen("templog.csv", "r")) !== FALSE) {
        $lastrow;
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

            $lastrow = $data;

        }
    }
    echo json_encode($lastrow);;
}

?>
