<?php


namespace Easybsn;

/**
 * BSN 接口
 * Class BsnLogic
 * @package Easybsn
 */
class BsnLogic
{
    /**
     * @var BaseSdk
     */
    private $query;

    /**
     * BsnLogic constructor.
     */
    public function __construct()
    {
        $this->query = BaseSdk::getInstance();
    }

    /**
     * 获取当前实例
     * @return BsnLogic
     */
    public static function getInstance()
    {
        return new self();
    }

    /**
     * 创建账户
     * @param $name
     * @return false|mixed
     */
    public function createAccount($name)
    {
        $data = [
            'name' => $name,
            'operation_id' => self::create_guid(),
        ];
        return $this->query->SignCurl('/v1beta1/account', 'post', '', $data);
    }

    /**
     * 批量创建链账户
     * /v1beta1/accounts
     */
    public function createAccounts()
    {
        $data = [
            'count' => 5,
            'operation_id' => self::create_guid(),
        ];
        return $this->query->SignCurl('/v1beta1/accounts', 'post', '', $data);
    }

    /**
     *查询链账户
     */
    public function getAccounts($account)
    {
        $p = [
            'offset' => '0', //游标，默认为 0
            'limit' => '10',  //每页记录数，默认为 10，上限为 50
            'account' => $account, //链账户地址
            'start_date' => '2022-05-19',//创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
            'end_date' => '2022-05-23', //创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
            'sort_by' => 'DATE_DESC',//排序规则：DATE_ASC / DATE_DESC
            'operation_id' => self::create_guid(),
        ];

        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }

        $query = substr($o, 0, -1);
        return $this->query->SignCurl('/v1beta1/accounts', 'get', $query, []);
    }

    /**
     * 查询链账户操作记录
     * https://apis.avata.bianjie.ai/v1beta1/accounts/history
     */
    public function accountsHistory($account)
    {
        $p = [
            'offset' => '0', //游标，默认为 0
            'limit' => '10',  //每页记录数，默认为 10，上限为 50
            'account' => $account, //链账户地址
            'module' => 'nft',//功能模块：account / nft
            'start_date' => '2022-05-19',//创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
            'end_date' => '2022-05-23', //创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
            'sort_by' => 'DATE_DESC',//排序规则：DATE_ASC / DATE_DESC
            'operation_id' => self::create_guid(),
            'tx_hash' => '',//Tx Hash
        ];

        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }

        $query = substr($o, 0, -1);
        return $this->query->SignCurl('/v1beta1/accounts/history', 'get', $query, []);
    }

    /**
     *上链交易结果查询
     * https://apis.avata.bianjie.ai/v1beta1/tx/{task_id}
     */
    public function queryTxByTaskId($task_id)
    {
        return $this->query->SignCurl("/v1beta1/tx/{$task_id}", 'get', "", []);
    }

    /**
     * 创建 NFT 类别
     * https://apis.avata.bianjie.ai/v1beta1/nft/classes
     *
     */
    public function createNftClass($data)
    {
        return $this->query->SignCurl('/v1beta1/nft/classes', 'post', '', $data);
    }

    /**
     * 查询 NFT 类别详情
     * https://apis.avata.bianjie.ai/v1beta1/nft/classes/{id}
     * @param $id
     */
    public function getNftClass($id)
    {
        $p = [
            'id' => $id, //游标，默认为 0
        ];
        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }

        $query = substr($o, 0, -1);
        $res = $this->query->SignCurl('/v1beta1/nft/classes/' . $id, 'get', $query, []);
        var_dump($res);
    }


    /**
     * 发行藏品
     * https://apis.avata.bianjie.ai/v1beta1/nft/nfts/{class_id}
     */
    public function publishNft($class_id, $classInfo)
    {
        $data = [
            'name' => $classInfo['name'],//NFT 名称,
            'uri' => $classInfo['uri'],//链外数据链接
            'uri_hash' => hash('sha256', $classInfo['uri']),//链外数据 Hash
            'data' => $classInfo['data'],//自定义链上元数据
            'recipient' => $classInfo['recipient'],//NFT 接收者地址，支持任一文昌链合法链账户地址，默认为 NFT 类别的权属者地址
            'tag' => $classInfo['tag'],
            'operation_id' => $classInfo['operation_id'],
        ];
        return $this->query->SignCurl("/v1beta1/nft/nfts/".$class_id, 'post', "", $data);
    }

    /**
     * 转让 NFT
     * https://apis.avata.bianjie.ai/v1beta1/nft/nft-transfers/{class_id}/{owner}/{nft_id}
     */
    public function transferNft($class_id, $owner, $nft_id, $nftInfo)
    {
        $data = [
            'recipient' => $nftInfo['recipient'],//NFT 接收者地址，支持任一文昌链合法链账户地址，默认为 NFT 类别的权属者地址
            'tag' => $nftInfo['tag'],
            'operation_id' => $nftInfo['operation_id'],
        ];
        return $this->query->SignCurl("/v1beta1/nft/nft-transfers/{$class_id}/{$owner}/{$nft_id}", 'post', "", $data);
    }

    /**
     * 编辑NFT
     * https://apis.avata.bianjie.ai/v1beta1/nft/NFT 接口/{class_id}/{owner}/{nft_id}
     */
    public function editNft()
    {
        $class_id = 1;
        $owner = '';
        $nft_id = '';
        $p = [
            'class_id' => $class_id, //NFT 类别 ID
            'owner' => '',//NFT 持有者地址
            'nft_id' => '',//NFT ID
        ];
        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }
        $query = substr($o, 0, -1);
        $data = [
            'recipient' => '',//NFT 接收者地址,
            'tag' => [

            ],
            'operation_id' => self::create_guid(),
        ];
        return $this->query->SignCurl("/v1beta1/nft/NFT 接口/{$class_id}/{$owner}/{$nft_id}", 'patch', $query, $data);
    }

    /**
     * 销毁 nft
     * https://apis.avata.bianjie.ai/v1beta1/nft/NFT 接口/{class_id}/{owner}/{nft_id}
     */
    public function delNft()
    {
        $class_id = 1;
        $p = [
            'class_id' => $class_id, //NFT 类别 ID
            'owner' => '',//NFT 持有者地址
            'nft_id' => '',//NFT ID
        ];
        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }
        $query = substr($o, 0, -1);
        $data = [
            'tag' => [

            ],
            'operation_id' => self::create_guid(),
        ];
        return $this->query->SignCurl("/v1beta1/nft/NFT 接口/{class_id}/{owner}/{nft_id}", 'delete', $query, $data);
    }

    /**
     * 查询 NFT
     * https://apis.avata.bianjie.ai/v1beta1/nft/NFT 接口
     */
    public function queryNft($class_id, $offset, $limit=10)
    {
        $p = [
            'offset' => $offset,
            'limit' => $limit,
            'id' => '',//NFT ID
            'class_id' => $class_id,//NFT 类别 ID
            'owner' => '',//NFT 持有者地址
            'tx_hash' => '',//创建 NFT 的 Tx Hash
            'start_date' => '',//NFT 创建日期范围 - 开始，yyyy-MM-dd（UTC 时间）
            'end_date' => '',//NFT 创建日期范围 - 结束，yyyy-MM-dd（UTC 时间）
            'sort_by' => '',//ID_ASC / ID_DESC / DATE_ASC / DATE_DESC
            'status' => '',//NFT 状态：active / burned，默认为 active
        ];

        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }
        $query = substr($o, 0, -1);
        return $this->query->SignCurl("/v1beta1/nft/nfts", 'get', $query, []);
    }

    /**
     * 查询 NFT 详情
     * https://apis.avata.bianjie.ai/v1beta1/nft/NFT 接口/{class_id}/{nft_id}
     */
    public function queryNftDetail($class_id, $nft_id)
    {
        $query = "";
        return $this->query->SignCurl("/v1beta1/nft/nfts/{$class_id}/{$nft_id}", 'get', $query, []);
    }

    /**
     * 查询 NFT 操作记录
     * https://apis.avata.bianjie.ai/v1beta1/nft/NFT 接口/{class_id}/{nft_id}/history
     */
    public function queryNftHistory()
    {
        $class_id = 1;
        $nft_id = '';
        $p = [
            'offset' => '0',//游标，默认为 0,
            'limit' => '10',//每页记录数，默认为 10，上限为 50
            'signer' => '',//Tx 签名者地址
            'tx_hash' => '',//NFT 操作 Tx Hash
            'operation' => '',//操作类型：mint / edit / transfer / burn
            'start_date' => '',//NFT 操作日期范围 - 开始，yyyy-MM-dd（UTC 时间）
            'end_date' => '',//NFT 操作日期范围 - 结束，yyyy-MM-dd（UTC 时间）
            'sort_by' => '',//排序规则：DATE_ASC / DATE_DESC
        ];

        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }

        $query = substr($o, 0, -1);
        return $this->query->SignCurl("/v1beta1/nft/NFT 接口/{$class_id}/{$nft_id}/history", 'get', $query, []);
    }

    /**
     * 充值接口
     * https://apis.avata.bianjie.ai/v1beta1/orders
     */
    public function buyEnergy()
    {
        $data = [
            'account' => '',//链账户地址
            'amount' => '',//购买金额 ，只能购买整数元金额；单位：分
            'order_type' => '',//充值类型：gas：能量值；business：业务费
            'order_id' => '',//自定义订单流水号，必需且仅包含数字、下划线及英文字母大/小写
        ];
        return $this->query->SignCurl("/v1beta1/orders", 'post', '', $data);
    }

    /**
     * 查询能量值/业务费购买结果列表
     * https://apis.avata.bianjie.ai/v1beta1/orders
     */
    public function queryOrders()
    {
        $p = [
            'offset' => '0',//游标，默认为 0,
            'limit' => '10',//每页记录数，默认为 10，上限为 50
            'start_date' => '',//NFT 操作日期范围 - 开始，yyyy-MM-dd（UTC 时间）
            'end_date' => '',//NFT 操作日期范围 - 结束，yyyy-MM-dd（UTC 时间）
            'sort_by' => '',//排序规则：DATE_ASC / DATE_DESC
            'status' => '',//订单状态：success 成功 / failed 失败 / pending 购买中 / locked 锁定中
        ];
        $o = '';
        foreach ($p as $k => $v) {
            $o .= "$k=" . $v . "&";
        }
        $query = substr($o, 0, -1);
        return $this->query->SignCurl("/v1beta1/orders", 'get', $query, []);
    }

    /**
     * 查询能量值/业务费购买结果
     * https://apis.avata.bianjie.ai/v1beta1/orders/{order_id}
     */
    public function queryOrderItem($order_id)
    {
        return $this->query->SignCurl("/v1beta1/orders/{$order_id}", 'get', '', []);
    }

 
    static function create_guid($namespace = '')
    {
        static $guid = '';
        $uid = uniqid("", true);
        $data = $namespace;
        //$data .= $_SERVER['REQUEST_TIME'];
        //$data .= $_SERVER['HTTP_USER_AGENT'];
        //$data .= getIP();
        //$data .= $_SERVER['REMOTE_PORT'];

        $hash = strtoupper(hash('ripemd128', $uid.$guid.md5($data)));
        $guid = substr($hash, 0, 8) . '-' .
            substr($hash, 8, 4) . '-' .
            substr($hash, 12, 4) . '-' .
            substr($hash, 16, 4) . '-' .
            substr($hash, 20, 12);

        return $guid;
    } 
}