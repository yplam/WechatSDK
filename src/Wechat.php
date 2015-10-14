<?php

/*
 * This file is part of the YPL\WechatSDK package.
 *
 * (c) yplam <yplam@yplam.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YPL\WechatSDK;

use GuzzleHttp\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use YPL\WechatSDK\Exception\WechatException;

class Wechat
{
    const API_URL = 'https://api.weixin.qq.com/cgi-bin';
    const API_HTTP_URL = 'http://api.weixin.qq.com/cgi-bin';

    /**
     * 可选的 $storage object，用来保存access_token
     * @var redis client
     */
    protected $storage;

    /**
     * 当使用storage时，key的前缀
     */
    protected $storagePrefix;

    /**
     * 可选的 $logger object，用来记录或者调试
     * @var monolog
     */
    protected $logger;

    /**
     * Config options，保存配置选择
     * @var Array
     */
    protected $options;

    /**
     * config $appid
     * @var string
     */
    protected $appid;

    /**
     * config $appsecret
     * @var string
     */
    protected $appsecret;

    /**
     * config $token
     * @var string
     */
    protected $token;

    // 加密方式
    protected $encryptType = null;

    /**
     * 加密key
     * @var string
     */
    protected $aesKey;



    /**
     * @var Array raw message，解密出来的消息数组
     */
    protected $rawMessage;

    protected $accessToken;

    /**
     * httClient用来封装http请求
     * @var Buzz\Client\ClientInterface
     */
    protected $httpClient;


    public function __construct(Client $httpClient, array $options)
    {
        $this->options = $options;
        $this->httpClient = $httpClient;

        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->token = isset($options['token'])?$options['token']:'';
        $this->encryptType = isset($options['encryptType'])?$options['encryptType']:null;
        $this->aesKey = isset($options['aesKey'])?$options['aesKey']:'';
        $this->storagePrefix = isset($options['storagePrefix']) ? $options['storagePrefix'] : 'wechat_';
    }

    /**
     * 设置储存对象
     * @param redis/memcache $storage 可以时redis/memcache等
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * 获取微信client_credential的token
     * @return String access_token值
     */
    public function getAccessToken()
    {
        $key = $this->storagePrefix . 'access_token';
        if($this->storage && $access_token = $this->storage->get($key)){
            return $access_token;
        }

        // 没有使用cache，但是已经请求过access_token，返回此次运行环境中的缓存
        if($this->accessToken){
            return $this->accessToken;
        }
        
        $response = $this->httpClient->request('GET', self::API_URL.'/token', [
            'query' => [
                'grant_type' => 'client_credential',
                'appid' => $this->appid,
                'secret' => $this->appsecret,
            ]
        ]);

        $token = $this->getResponseContent($response);

        if(!isset($token['access_token'])){
            throw new WechatException('get_access_token_error');
        }

        // 就算没有使用外部cache，我们也应该将access_token保存在context中
        $this->accessToken = $token['access_token'];


        // 超时时间减100秒，防止因获取消息的延迟而导致服务端先过期
        $this->storage && $this->storage->set($key, $token['access_token'], (int)$token['expires_in'] - 100);
        return $token['access_token'];
    }

    /**
     * 请求封装，返回response
     * @param  string $url     请求地址
     * @param  array  $query   请求url参数
     * @param  string $content post内容，可以是form，也可以时raw
     * @param  array  $headers header
     * @param  string $method  GET 或 POST
     * @return array    
     */
    public function rawRequest($url, $query = array(), $content = null, $files = array(), $method = null)
    {
        if (null === $method) {
            $method = ($content || $files ) ? 'POST' : 'GET';
        }
        if( !isset($query['access_token']) ){
            $query['access_token'] = $this->getAccessToken();
        }
        $data = array();
        $data['query'] = $query;
        if($content){
            if(is_array($content)){
                $data['form_params'] = $content;
            }
            else{
                $data['body'] = $content;
            }
        }
        if($files){
            $data['multipart'] = $files;
        }
        return $this->httpClient->request($method, $url, $data);
    }

    /**
     * 请求封装，返回json_decode后的值
     * @param  string $url     请求地址
     * @param  array  $query   请求url参数
     * @param  string $content post内容
     * @param  array  $headers header
     * @param  string $method  GET 或 POST
     * @return ResponseInterface  
     */
    public function request($url, $query = array(), $content = null, $files = array(), $method = null)
    {
        $response = $this->rawRequest($url, $query, $content, $files, $method);
        return $this->getResponseContent($response);
    }

    /**
     * 以数组的形式获取微信服务器发送的Message
     * @return array rawMessage
     */
    public function getRawMessage()
    {
        $signature = isset($_GET['msg_signature']) ? $_GET['msg_signature']
            : ( isset($_GET['signature']) ? $_GET['signature'] : '');
        $timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        $nonce = isset($_GET['nonce']) ? $_GET['nonce'] : '';
        $encryptStr = '';
        $rawMessage = array();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $strMessage = file_get_contents("php://input");
            $rawMessage = (array)simplexml_load_string($strMessage, 'SimpleXMLElement', LIBXML_NOCDATA);
            // 校验message
            if(empty($rawMessage)){
                throw new WechatException('no_message_receive');
            }

            if(isset($rawMessage['Encrypt'])){
                $this->encryptType = 'aes';
                $encryptStr = $rawMessage['Encrypt'];

                $pc = new Prpcrypt($this->aesKey);
                $result = $pc->decrypt($encryptStr,$this->appid);
                if (!isset($result[0]) || ($result[0] != 0)) {
                    throw new WechatException('decrypt_error');
                }
                $rawMessage = (array)simplexml_load_string($result[1], 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$this->appid){
                    $this->appid = $result[2];//为了没有appid的订阅号。
                }
            }

            if(empty($rawMessage['MsgType'])){
                throw new WechatException('message_verify_error');
            }
        }

        $token = $this->token;
        $validArr = array($token, $timestamp, $nonce, $encryptStr);
        sort($validArr, SORT_STRING);
        $validStr = sha1(implode( $validArr ));
        if( $validStr !== $signature ){
            throw new WechatException('message_verify_error');
        }
        $this->rawMessage = $rawMessage;
        return $rawMessage;
    }

    

    /**
     * 将HttpClient返回的Response解码，返回数组
     */
    public function getResponseContent(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();
        if (!$content) {
            return array();
        }
        $response = json_decode($content, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            parse_str($content, $response);
        }
        return $response;
    }


    public static function xmlSafeStr($str)
    {
        return '<![CDATA['.preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/",'',$str).']]>';
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .=  ( is_array($val) || is_object($val)) ? self::data_to_xml($val)  : self::xmlSafeStr($val);
            list($key, ) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }

    public function toEncryptXML($responseMessage)
    {
        $encryptMessage = array();
        $pc = new Prpcrypt($this->aesKey);
        $encrypted = $pc->encrypt($this->toXML($responseMessage), $this->appid);
        $ret = $encrypted[0];
        if ($ret != 0) {
            return 'error';
        }
        $timestamp = time();
        $nonce = rand(77,999)*rand(605,888)*rand(11,99);
        $encrypt = $encrypted[1];
        $tmpArr = array($this->token, $timestamp, $nonce,$encrypt);
        sort($tmpArr, SORT_STRING);
        $signature = implode($tmpArr);
        $signature = sha1($signature);
        $encryptMessage['Encrypt'] = $encrypt;
        $encryptMessage['MsgSignature'] = $signature;
        $encryptMessage['TimeStamp'] = $timestamp;
        $encryptMessage['nonce'] = $nonce;
        return sprintf("<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>", $encrypt, $signature, $timestamp, $nonce);
    }

    public function toXML($responseMessage)
    {
        return '<xml>'.self::data_to_xml($responseMessage).'</xml>';
    }

    public function response($responseMessage)
    {
        if($this->encryptType === 'aes'){
            return $this->toEncryptXML($responseMessage);
        }
        return $this->toXML($responseMessage);
    }


    /************************************************
     * 以下为对一些常用功能的封装
     ************************************************/

    //// 菜单相关操作

    /**
     * 获取菜单
     * @return string json菜单数据
     *
     * {"menu":{"button":[{"type":"click","name":"今日歌曲","key":"V1001_TODAY_MUSIC","sub_button":[]},{"type":"click","name":"歌手简介","key":"V1001_TODAY_SINGER","sub_button":[]},{"name":"菜单","sub_button":[{"type":"view","name":"搜索","url":"http://www.soso.com/","sub_button":[]},{"type":"view","name":"视频","url":"http://v.qq.com/","sub_button":[]},{"type":"click","name":"赞一下我们","key":"V1001_GOOD","sub_button":[]}]}]}}
     */
    public function getMenu()
    {
        return $this->request(self::API_URL . '/menu/get');
    }

    /**
     * 上传菜单
     * @param string $jsonMenu 已经json_encode的字符串
     *
     * {
         "button":[
             {
                  "type":"click",
                  "name":"今日歌曲",
                  "key":"V1001_TODAY_MUSIC"
              },
              {
                   "name":"菜单",
                   "sub_button":[
                   {
                       "type":"view",
                       "name":"搜索",
                       "url":"http://www.soso.com/"
                    },
                    {
                       "type":"view",
                       "name":"视频",
                       "url":"http://v.qq.com/"
                    },
                    {
                       "type":"click",
                       "name":"赞一下我们",
                       "key":"V1001_GOOD"
                    }]
               }]
         }
     */
    public function setMenu($menu)
    {
        if(is_array($menu)){
            $menu = json_encode($menu);
        }
        return $this->request(self::API_URL . '/menu/create', array(), $menu);
    }

    /**
     * 删除菜单
     * @return array 菜单返回结果
     */
    public function deleteMenu()
    {
        return $this->request(self::API_URL . '/menu/delete');
    }

    //// 临时素材接口

    /**
     * 上传临时素材
     * @param  string $file        文件名
     * @param  string $type        类型 image video 等
     * @return Array             返回结果
     *
     * {"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
     */
    public function uploadMedia($file, $type)
    {
        $files = [
            [
                'name'     => 'media',
                'contents' => fopen($file, 'r')
            ],
            [
                'name' => 'type',
                'contents' => $type
            ]
        ];
        return $this->request(self::API_URL . '/media/upload', array(), null, $files);
    }

    /**
     * 获取素材内容
     * @param  string $mediaId 素材ID
     * @param  string $type    素材类型，当为video时会区分对待
     * @return HttpResponse    Response 主体，当无错误时会返回file相关http信息
     */
    public function getMedia($mediaId, $type = null)
    {
        if($type == 'video'){
            $baseUrl = self::API_URL;
        }
        else{
            $baseUrl = self::API_HTTP_URL;
        }
        $response = $this->rawRequest($baseUrl . '/media/get', array('media_id' => $mediaId));
        return $response;
    }

    //// 永久素材接口

    /**
     * 上传图片
     * @param  string $image 图片路径
     * @return array 返回结果
     *
     *  {
            "url":  "http://mmbiz.qpic.cn/mmbiz/gLO17UPS6FS2xsypf378iaNhWacZ1G1UplZYWEYfwvuU6Ont96b1roYs CNFwaRrSaKTPCUdBK9DgEHicsKwWCBRQ/0"
        }
     */
    public function uploadImg($image)
    {
        $files = [
            [
                'name'     => 'media',
                'contents' => fopen($image, 'r')
            ],
        ];
        return $this->request(self::API_URL . '/media/uploadimg', array(), null, $files);
    }

    /**
     * 上传永久素材
     * @param  string $file        文件名
     * @param  string $type        类型 image video 等
     * @param  string $fileName    文件名
     * @param  string $contentType 文件类型
     * @return Array             返回结果
     *  {
          "media_id":MEDIA_ID,
          "url":URL
        }
     */
    public function addMaterial($file, $type, $options = array())
    {

        $files = [
            [
                'name'     => 'media',
                'contents' => fopen($file, 'r')
            ],
            [
                'name' => 'type',
                'contents' => $type
            ]
        ];
        if($type == 'video'){
            $description = array();
            $description['title'] = isset($options['title']) ? $options['title'] : '';
            $description['introduction'] = isset($options['introduction']) ? $options['introduction'] : '';
            $files[] = array(
                'name' => 'description',
                'contents' => json_encode($description)
            );
        }
        return $this->request(self::API_URL . '/material/add_material', array(), null, $files);
    }

    public function addNews($articles)
    {
        if(is_array($articles)){
            $articles = json_encode($articles);
        }
        return $this->request(self::API_URL . '/material/add_news', array(), $articles);
    }

    // 获取用户信息
    public function getUserInfo($openid, $lang='zh_CN')
    {
        return $this->request(self::API_URL . '/user/info', array(
            'lang'=>$lang,
            'openid' => $openid,
            ));
    }

}

