EASYBSN
介绍
基于ThinkPHP6框架
 

安装教程
```
composer require cssharp/easybsn
```

使用demo
```
use Easybsn\BsnLogic;

$obj = new BsnLogic($apiKey="你的key", $apiSecret="你的密钥");
$rt = $obj->createAccount("nick");
var_dump($rt);
```

