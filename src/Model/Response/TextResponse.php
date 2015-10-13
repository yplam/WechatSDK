<?php

namespace YPL\WechatSDK\Model\Response;


/**
 * TextResponse
 *  <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[fromUser]]></FromUserName>
    <CreateTime>12345678</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[你好]]></Content>
    </xml>
 */
class TextResponse extends BaseResponse
{


    /**
     * @var string
     */
    private $content;

    public function __construct(array $rawResponse = array())
    {
        parent::__construct($rawResponse);
        isset($rawResponse['Content']) && $this->content = $rawResponse['Content'];
    }

    public function getRawResponse()
    {
        $rawResponse = parent::getRawResponse();
        $rawResponse['MsgType'] = 'text';
        $rawResponse['Content'] = $this->content;
        return $rawResponse;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return TextResponse
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}
