<?php

namespace YPL\WechatSDK\Model\Response;


/**
 * VoiceResponse
 *  <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[fromUser]]></FromUserName>
    <CreateTime>12345678</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    <Voice>
    <MediaId><![CDATA[media_id]]></MediaId>
    </Voice>
    </xml>
 */
class VoiceResponse extends BaseResponse
{

    /**
     * @var string
     */
    private $mediaId;

    public function __construct(array $rawResponse = array())
    {
        parent::__construct($rawResponse);
        isset($rawResponse['MediaId']) && $this->mediaId = $rawResponse['MediaId'];
    }

    public function getRawResponse()
    {
        $rawResponse = parent::getRawResponse();
        $rawResponse['MsgType'] = 'voice';
        $rawResponse['Voice'] = array();
        $rawResponse['Voice']['MediaId'] = $this->mediaId;
        return $rawResponse;
    }


    /**
     * Set mediaId
     *
     * @param string $mediaId
     * @return VoiceResponse
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * Get mediaId
     *
     * @return string 
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }
}
