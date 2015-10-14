WechatSDK 微信开发SDK
=======================

Yet Another WechatSDK !

网络上开源的微信公众平台代码已经有好多，其中也不乏优秀者，然而却找不到合适在Symfony2中
使用者，于是就有了这个项目。当然，这些代码也可以很容易的应用在其他框架（CMS）中。

功能比较简单，实现了对消息、回复，以及常用操作的封装，以后会根据需要慢慢完善。同时，
对未封装的操作，提供request方法，可以非常方便的调用。

与其他微信SDK有点区别的是，没有对具体功能进行实现，因为Symfony2（或者你的框架）在这方面
更在行。

此SDK依赖于 guzzle/guzzle 库，一个优秀的HTTP Client库。

用法
-----


```php

    use YPL\WechatSDK\Wechat;
    use YPL\WechatSDK\Model\MessageManager;
    use YPL\WechatSDK\Model\Response\TextResponse;
    use GuzzleHttp\Client;
    
    $httpClient = new Client();
    $wechat = new Wechat($httpClient, array(
        'appid' => '%appid%',
        'appsecret' => '%appsecret%',
        'token' => '%token%',
    ));
    
    // 可选的redis缓存
    $redis = new \Redis();
    /* 一些配置... */
    $wechat->setStorage($redis);
    
    // 回复消息
    $rawMessage = $wechat->getRawMessage();
    $messageManager = new MessageManager();
    $message = $messageManager->createFromRawMessage($rawMessage);
    $response = new TextResponse(array(
        'ToUserName' => $message->getFromUserName(),
        'FromUserName' => $message->getToUserName(),
        'Content' => 'Pong',
    ));
    print($wechat->response($response->getRawResponse()));
    
    // 上传临时资源
    $result = $wechat->uploadMedia('test.png', 'image');

```
-----

