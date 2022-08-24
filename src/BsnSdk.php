<?php


namespace EasyBsnComposer;


class BaseSdk
{
    /**
     * 错误信息
     * @var string
     */
    private static $curlError;

    /**
     * header头信息
     * @var string
     */
    private static $headerStr;

    /**
     * 请求状态
     * @var int
     */
    private static $status;

    /**
     * @var
     */
    private static $instance;


    /**
     * @var
     */
    private static $apiKey="你的key";

    /**
     * @var
     */
    private static $apiSecret="你的ecret";

    /**
     * @return BaseQuery
     */
    public static function getInstance()
    {
        return new self();
    }


    /**
     *
     */
    public function SignCurl($path,$method='get', $query = "", $data = [])
    {
        $domain = "https://apis.avata.bianjie.ai"; // 域名
        $url = $domain . $path;
        if($query) $url = $domain . $path . "?" . $query;
        $ch = curl_init($url);
        $method = strtoupper($method);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // 获取当前时间戳，单位:ms
        $timestamp = $this->getMillisecond();
        $params = ["path_url" => $path];
        if (!empty($query)) {
            $queryArr = explode("&", $query);
            foreach ($queryArr as $v) {
                $tmpArr = explode("=", $v);
                // 组装 query
                $params["query_{$tmpArr[0]}"] = $tmpArr[1];
            }
        }
        if (!empty($data)) {
            // 组装 post body
            foreach ($data as $k => $v) {
                $params["body_{$k}"] = $v;
            }
        }

        // 按 key 进行排序
        ksort($params);
        $hexHash = hash("sha256", "{$timestamp}" . self::$apiSecret);
        if (count($params) > 0) {
            // 序列化且不编码
            $s = json_encode($params, JSON_UNESCAPED_UNICODE);
            $hexHash = hash("sha256", stripcslashes($s . "{$timestamp}" . self::$apiSecret));
        }
        // 设置请求头
        $header = ['Content-Type:application/json']; // 请求头
        $header[] = "X-Api-Key:".self::$apiKey;
        $header[] = "X-Signature:{$hexHash}";
        $header[] = "X-Timestamp:".(string)$timestamp;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        //返回抓取数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //输出header头信息
        curl_setopt($ch, CURLOPT_HEADER, true);

        //TRUE 时追踪句柄的请求字符串，从 PHP 5.1.3 开始可用。这个很关键，就是允许你查看请求header
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if(!empty($data))  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        //https请求
        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        self::$curlError = curl_error($ch);
        list($content, $status) = [curl_exec($ch), curl_getinfo($ch),curl_close($ch)];
        self::$status = $status;
        self::$headerStr = trim(substr($content, 0, $status['header_size']));
        $content = trim(substr($content, $status['header_size']));

        return (intval($status["http_code"]) === 200) ? json_decode($content,true) : false;
    }

    /**
     * @return float
     */
    public function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }


    /**
     * 数据请求
     * @param $url
     * @param string $method
     * @param $data
     * @param array $header
     * @param int $timeout
     * @return false|string
     */
    public static function request($url, $method = 'get', $data=[], $header=[], $timeout = 15)
    {
        self::$status = null;
        self::$curlError = null;
        self::$headerStr = null;

        $curl = curl_init($url);

        $method = strtoupper($method);

        //请求方式
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        //post请求
        if ($method == 'POST') curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        //超时时间
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        //设置header头

        // 按 key 进行排序

        ksort($params);
        list($t1, $t2) = explode(' ', microtime());
        $timestamp = (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        $hexHash = hash("sha256", "{$timestamp}" . self::$apiSecret);

        $header[] = "X-Api-Key:".self::$apiKey;
        $header[] = "X-Signature:{$hexHash}";
        $header[] = "X-Timestamp:{$timestamp}";

        array_push($header,'Content-Type: application/json');
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);


        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        //返回抓取数据
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //输出header头信息
        curl_setopt($curl, CURLOPT_HEADER, true);

        //TRUE 时追踪句柄的请求字符串，从 PHP 5.1.3 开始可用。这个很关键，就是允许你查看请求header
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);




        //https请求
        if (1 == strpos("$" . $url, "https://")) {

            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        }
        self::$curlError = curl_error($curl);

        list($content, $status) = [curl_exec($curl), curl_getinfo($curl), curl_close($curl)];

        self::$status = $status;
        self::$headerStr = trim(substr($content, 0, $status['header_size']));
        $content = trim(substr($content, $status['header_size']));

        return (intval($status["http_code"]) === 200) ? $content : false;
    }

}