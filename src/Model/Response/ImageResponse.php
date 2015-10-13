<?php

namespace YPL\WechatSDK\Model\Response;


/**
 * ImageResponse
 *  <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[fromUser]]></FromUserName>
    <CreateTime>12345678</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    <Image>
    <MediaId><![CDATA[media_id]]></MediaId>
    </Image>
    </xml>
 */
class ImageResponse extends BaseResponse
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
        $rawResponse['MsgType'] = 'image';
        $rawResponse['Image'] = array();
        $rawResponse['Image']['MediaId'] = $this->mediaId;
        return $rawResponse;
    }

    /**
     * Set mediaId
     *
     * @param string $mediaId
     * @return ImageResponse
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
