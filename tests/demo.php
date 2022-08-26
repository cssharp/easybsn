<?php

declare(strict_types=1);


use Easybsn\BsnLogic;

$obj = new BsnLogic($apiKey="你的key", $apiSecret="你的密钥");
$rt = $obj->createAccount("nick");
var_dump($rt);


