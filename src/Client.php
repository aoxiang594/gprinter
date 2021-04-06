<?php

namespace Aoxiang\Gprinter;

class Client
{
    /** @var \GuzzleHttp\Client $httpClient */
    private $httpClient = null;
    private $host = 'http://api.poscom.cn'; //接口IP或域名
    private $port = '80';                   //接口端口

    private $reqTime = null;                    //请求时间
    private $securityCode = '';                 //安全检验码
    private $apiKey = '';                       //api密钥
    private $memberCode = '';                   //商户编码
    private $mode = '2';                        //打印类型
    private $deviceID = '';                     //终端编号
    private $msgNo = '';                        //订单编号
    private $msgDetail = '';                    //打印内容
    private $charset = '1';                     //编码格式 （票据机型请选择 1：gb18030，标签机GP-CH421D请选择 4：utf-8）
    private $DemoMsgDetail = '';                //打印内容
    private $action = 'sendMsg';                //发送数据到打印机

    /**
     * Client constructor.
     *
     * @param  string  $apiKey
     * @param  string  $memberCode
     */
    public function __construct(string $apiKey, string $memberCode)
    {
        $this->apiKey     = $apiKey;
        $this->memberCode = $memberCode;
        $this->httpClient = new \GuzzleHttp\Client();
    }

    /**
     * 打印标签
     *
     * @param $data
     */
    public function tag(string $template)
    {

        $this->setMsgDetail($template);

        $data = [
            'msgDetail' => $this->msgDetail,
            'reprint'   => 1,
        ];

        return $this->send($this->host . '/apisc/sendMsg', $data);
    }

    public function ticket()
    {
        return $this->send($this->host . '/apisc/sendMsg', $data);
    }

    /**
     * @param  string  $memberCode
     *
     * @return $this
     */
    public function setMemberCode(string $memberCode)
    {
        $this->memberCode = $memberCode;

        return $this;
    }


    /**
     * @param  string  $deviceId
     *
     * @return $this
     */
    public function setDeviceId(string $deviceId)
    {
        $this->deviceID = $deviceId;

        return $this;
    }

    public function setMsgDetail(string $detail)
    {
        $this->msgDetail = mb_convert_encoding($detail, 'UTF-8');

        return $this;
    }

    /**
     * @param $url
     * @param $data
     */
    protected function send(string $url, array $data)
    {
        if( isset($data['deviceID']) ){
            $this->deviceID = $data['deviceID'];
        }
        $data['deviceID']     = $this->deviceID;
        $this->reqTime        = $this->getMillisecond();
        $data['securityCode'] = $this->getSecurityCode();
        $data['reqTime']      = $this->reqTime;
        $data['memberCode']   = $this->memberCode;
        $data['mode']         = $this->mode;
        $data['charset']      = $this->charset;
        
        $response = $this->httpClient->post($url,
            [
                'headers'     => [
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $data,
            ]
        );
        if( $response->getStatusCode() === 200 ){
            $body = $response->getBody()->getContents();
            $body = json_decode($body, true);
            if( is_null($body) ){
                throw new Exception("请求异常");
            } else {
                if( empty($body['code']) ){
                    return true;
                }
                throw new Exception($body['msg']);
            }
        }
    }

    public function setMsgNo($msgNo)
    {
        $this->msgNo = $msgNo;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSecurityCode()
    {
        if( empty($this->memberCode) ){
            throw new Exception('商户编码不得为空');
        }
        if( empty($this->deviceID) ){
            throw new Exception('终端编号不得为空');
        }
        if( empty($this->apiKey) ){
            throw new Exception('Api Key 不得为空');
        }


        return md5($this->memberCode . $this->deviceID . $this->msgNo . $this->reqTime . $this->apiKey);
    }

    /**
     * @return float
     */
    protected function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());

        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}