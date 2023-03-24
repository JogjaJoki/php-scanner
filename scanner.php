<?php
    require_once './SqliDetector.php';

    $detector = new SqliDetector();
    $detector->setUrl('https://www.equitytower.co.id/gallery-detail.php?id=16');
    $res = $detector->execute();
    var_dump($res);