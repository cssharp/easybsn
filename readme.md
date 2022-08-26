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

#1. 创建账号
$rt = $obj->createAccount("nick");

#2. 创建分类
$nft_class = [
            'name' => "名称", //名称
            'symbol' =>  '', //标识
            'description' =>  '',//描述
            'uri' =>   '',//链外数据链接
            'data' =>   '',//自定义链上元数据
            'owner' => "所有者地址",
            'operation_id' => create_guid(),
        ];
$rt = $obj->createNftClass($nft_class);

#3. 发布商品上链
$nftInfo = [
    "name"=>"名称",
    "uri"=> "资源url",
    "data"=>"资源描述",
    'recipient' => "接受者地址",
    'tag' => [
        'nftlevel' => 'general'
    ],
    'operation_id' => create_guid(),
];
$obj->publishNft("分类地址", $nftInfo);

```

