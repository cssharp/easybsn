<?php

declare(strict_types=1);

namespace EasyBsnComposer;

use EasyBsnComposer\BsnLogic;

class Demo
{
    private $vendorPath;
    private $manifestPath;

    public function testUnlink()
    {
        //创建账号
        $res = BsnLogic::getInstance()->createAccount("hello");
    }

    public function __construct()
    {
        echo "hello";
        $this->testUnlink();
    }
}

$d = new Demo();

