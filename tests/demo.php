<?php

declare(strict_types=1);

use Easybsn\BsnLogic;

$obj = new BsnLogic();
$rt = $obj->createAccount("nick");
var_dump($rt);



