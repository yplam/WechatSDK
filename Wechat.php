<?php

/*
 * This file is part of the yplamWechatBundle package.
 *
 * (c) yplam <yplam@yplam.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YPL\WechatSDK;

use Buzz\Client\ClientInterface as HttpClientInterface;
use Buzz\Message\MessageInterface as HttpMessageInterface;
use Buzz\Message\Request as HttpRequest;
use Buzz\Message\Form\FormRequest;
use Buzz\Message\RequestInterface as HttpRequestInterface;
use Buzz\Message\Response as HttpResponse;
use Buzz\Message\Form\FormUpload;

use YPL\WechatSDK\Event\MessageEvent;
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


    public function __construct(HttpClientInterface $httpClient, array $options)
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
        $response = $this->httpRequest($this->normalizeUrl(self::API_URL.'/token', array(
            'grant_type'=>'client_credential',
            'appid' => $this->appid,
            'secret' => $this->appsecret,
        )));
        $token = $this->getResponseContent($response);
        if(!isset($token['access_token'])){
            throw new WechatException('get_access_token_error');
        }
        // 超时时间减100秒，防止因获取消息的延迟而导致服务端先过期
        $this->storage && $this->storage->set($key, $token['access_token'], (int)$token['expires_in'] - 100);
        return $token['access_token'];
    }

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

            if(isset($this->rawMessage['Encrypt'])){
                $this->encryptType = 'aes';
                $encryptStr = $this->rawMessage['Encrypt'];
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

    public function request($url, $params = array(), $content = null, $headers = array(), $method = null)
    {
        $params['access_token'] = $this->getAccessToken();
        $url = $this->normalizeUrl($url,$params);
        return $this->httpRequest($url, $content, $headers, $method);
    }


    public function upload($url, $params = array(), $fields)
    {
        $params['access_token'] = $this->getAccessToken();
        $url = $this->normalizeUrl($url,$params);
        return $this->uploadRequest($url, $fields);
    }

    /**
     * 发送HTTP请求
     *
     * @return HttpResponse The response content
     */
    public function uploadRequest($url, $fields)
    {
        $request  = new FormRequest(HttpRequestInterface::METHOD_POST, $url);
        $response = new HttpResponse();
        $headers = array(
            'User-Agent: YPLWechatSDK',
        );
        $request->setHeaders($headers);
        $request->setFields($fields);
        $this->httpClient->send($request, $response);
        return $response;
    }

    /**
     * 发送HTTP请求
     *
     * @return HttpResponse The response content
     */
    public function httpRequest($url, $content = null, $headers = array(), $method = null)
    {
        if (null === $method) {
            $method = null === $content ? HttpRequestInterface::METHOD_GET : HttpRequestInterface::METHOD_POST;
        }
        $request  = new HttpRequest($method, $url);
        $response = new HttpResponse();
        $contentLength = 0;
        if (is_string($content)) {
            $contentLength = strlen($content);
        } elseif (is_array($content)) {
            $contentLength = strlen(implode('', $content));
        }
        $headers = array_merge(
            array(
                'User-Agent: YPLWechatSDK',
                'Content-Length: ' . $contentLength,
            ),
            $headers
        );
        $request->setHeaders($headers);
        $request->setContent($content);
        $this->httpClient->send($request, $response);
        return $response;
    }

    /**
     * 构造url
     * @param  [type] $url        [description]
     * @param  array  $parameters [description]
     * @return [type]             [description]
     */
    public function normalizeUrl($url, array $parameters = array())
    {
        $normalizedUrl = $url;
        if (!empty($parameters)) {
            $normalizedUrl .= (false !== strpos($url, '?') ? '&' : '?').http_build_query($parameters, '', '&');
        }
        return $normalizedUrl;
    }

    /**
     * Get the 'parsed' content based on the response headers.
     *
     * @param HttpMessageInterface $rawResponse
     *
     * @return array
     */
    public function getResponseContent(HttpMessageInterface $rawResponse)
    {
        // First check that content in response exists, due too bug: https://bugs.php.net/bug.php?id=54484
        $content = $rawResponse->getContent();
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
        $response = $this->request(self::API_URL . '/menu/get');
        $menu = $this->getResponseContent($response);
        return $menu;
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
        $response = $this->request(self::API_URL . '/menu/create', array(), $menu);
        $result = $this->getResponseContent($response);
        return $result;
    }

    /**
     * 删除菜单
     * @return array 菜单返回结果
     */
    public function deleteMenu()
    {
        $response = $this->request(self::API_URL . '/menu/delete');
        $result = $this->getResponseContent($response);
        return $result;
    }

    //// 临时素材接口

    /**
     * 上传临时素材
     * @param  string $file        文件名
     * @param  string $type        类型 image video 等
     * @param  string $fileName    文件名
     * @param  string $contentType 文件类型
     * @return Array             返回结果
     *
     * {"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
     */
    public function uploadMedia($file, $type, $fileName = '', $contentType = null)
    {
        $uploadFile = new FormUpload($file, $contentType);
        if($fileName){
            $uploadFile->setFilename($fileName);
        }
        $fields = array();
        $fields['media'] = $uploadFile;
        $fields['type'] = $type;
        $response = $this->upload(self::API_URL . '/media/upload', array(), $fields);
        $result = $this->getResponseContent($response);
        return $result;
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
        $response = $this->request($baseUrl . '/media/get', array('media_id' => $mediaId));
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
        $uploadFile = new FormUpload($image);
        $fields = array();
        $fields['media'] = $uploadFile;
        $response = $this->upload(self::API_URL . '/media/uploadimg', array(), $fields);
        $result = $this->getResponseContent($response);
        return $result;
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
    public function addMaterial($file, $type, $options = array(), $fileName = '', $contentType = null)
    {
        $uploadFile = new FormUpload($file, $contentType);
        if($fileName){
            $uploadFile->setFilename($fileName);
        }
        $fields = array();
        $fields['media'] = $uploadFile;
        $fields['type'] = $type;
        if($type == 'video'){
            $description = array();
            $description['title'] = isset($options['title']) ? $options['title'] : '';
            $description['introduction'] = isset($options['introduction']) ? $options['introduction'] : '';
            $fields['description'] = json_encode($description);
        }
        $response = $this->upload(self::API_URL . '/material/add_material', array(), $fields);
        $result = $this->getResponseContent($response);
        return $result;
    }

    public function addNews($articles)
    {
        if(is_array($articles)){
            $articles = json_encode($articles);
        }
        $response = $this->request(self::API_URL . '/material/add_news', array(), $articles);
        $result = $this->getResponseContent($response);
        return $result;
    }

    // 获取用户信息
    public function getUserInfo($openid, $lang='zh_CN')
    {
        $response = $this->request(self::API_URL . '/user/info', array(
            'lang'=>$lang,
            'openid' => $openid,
            ));
        $result = $this->getResponseContent($response);
        return $result;
    }

}

