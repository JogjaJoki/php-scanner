<?php
    require_once './SqliDetector.php';

    $detector = new SqliDetector();
    $detector->setUrl('http://192.168.1.21:8000/register.php');
    $detector->setSqlError('error.txt');
    $detector->setPayload('payload.txt');
    $res = $detector->execute();
    var_dump($res);