<?php

namespace YPL\WechatSDK\Model\Response;


/**
 * VideoResponse
 *  <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[fromUser]]></FromUserName>
    <CreateTime>12345678</CreateTime>
    <MsgType><![CDATA[video]]></MsgType>
    <Video>
    <MediaId><![CDATA[media_id]]></MediaId>
    <Title><![CDATA[title]]></Title>
    <Description><![CDATA[description]]></Description>
    </Video> 
    </xml>
 */
class VideoResponse extends BaseResponse
{

    /**
     * @var string
     */
    private $mediaId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    public function __construct(array $rawResponse = array())
    {
        parent::__construct($rawResponse);
        isset($rawResponse['Title']) && $this->title = $rawResponse['Title'];
        isset($rawResponse['Description']) && $this->description = $rawResponse['Description'];
        isset($rawResponse['MediaId']) && $this->mediaId = $rawResponse['MediaId'];

    }

    public function getRawResponse()
    {
        $rawResponse = parent::getRawResponse();
        $rawResponse['MsgType'] = 'video';
        $rawResponse['Video'] = array();
        $rawResponse['Video']['MediaId'] = $this->mediaId;
        $rawResponse['Video']['Title'] = $this->title;
        $rawResponse['Video']['Description'] = $this->description;
        return $rawResponse;
    }


    /**
     * Set mediaId
     *
     * @param string $mediaId
     * @return VideoResponse
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

    /**
     * Set title
     *
     * @param string $title
     * @return VideoResponse
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return VideoResponse
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
