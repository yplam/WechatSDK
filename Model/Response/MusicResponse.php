<?php

namespace YPL\WechatSDK\Model\Response;


/**
 * MusicResponse
 *  <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[fromUser]]></FromUserName>
    <CreateTime>12345678</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    <Music>
    <Title><![CDATA[TITLE]]></Title>
    <Description><![CDATA[DESCRIPTION]]></Description>
    <MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
    <HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
    <ThumbMediaId><![CDATA[media_id]]></ThumbMediaId>
    </Music>
    </xml>
 */
class MusicResponse extends BaseResponse
{



    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $musicUrl;

    /**
     * @var string
     */
    private $hQMusicUrl;

    /**
     * @var string
     */
    private $thumbMediaId;

    public function __construct(array $rawResponse = array())
    {
        parent::__construct($rawResponse);
        isset($rawResponse['Title']) && $this->title = $rawResponse['Title'];
        isset($rawResponse['Description']) && $this->description = $rawResponse['Description'];
        isset($rawResponse['MusicUrl']) && $this->musicUrl = $rawResponse['MusicUrl'];
        isset($rawResponse['HQMusicUrl']) && $this->hQMusicUrl = $rawResponse['HQMusicUrl'];
        isset($rawResponse['ThumbMediaId']) && $this->thumbMediaId = $rawResponse['ThumbMediaId'];

    }

    public function getRawResponse()
    {
        $rawResponse = parent::getRawResponse();
        $rawResponse['MsgType'] = 'music';
        $rawResponse['Music'] = array();
        $rawResponse['Music']['Title'] = $this->title;
        $rawResponse['Music']['Description'] = $this->description;
        $rawResponse['Music']['MusicUrl'] = $this->musicUrl;
        $rawResponse['Music']['HQMusicUrl'] = $this->hQMusicUrl;
        $rawResponse['Music']['ThumbMediaId'] = $this->thumbMediaId;
        return $rawResponse;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return MusicResponse
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
     * @return MusicResponse
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

    /**
     * Set musicUrl
     *
     * @param string $musicUrl
     * @return MusicResponse
     */
    public function setMusicUrl($musicUrl)
    {
        $this->musicUrl = $musicUrl;

        return $this;
    }

    /**
     * Get musicUrl
     *
     * @return string 
     */
    public function getMusicUrl()
    {
        return $this->musicUrl;
    }

    /**
     * Set hQMusicUrl
     *
     * @param string $hQMusicUrl
     * @return MusicResponse
     */
    public function setHQMusicUrl($hQMusicUrl)
    {
        $this->hQMusicUrl = $hQMusicUrl;

        return $this;
    }

    /**
     * Get hQMusicUrl
     *
     * @return string 
     */
    public function getHQMusicUrl()
    {
        return $this->hQMusicUrl;
    }

    /**
     * Set thumbMediaId
     *
     * @param string $thumbMediaId
     * @return MusicResponse
     */
    public function setThumbMediaId($thumbMediaId)
    {
        $this->thumbMediaId = $thumbMediaId;

        return $this;
    }

    /**
     * Get thumbMediaId
     *
     * @return string 
     */
    public function getThumbMediaId()
    {
        return $this->thumbMediaId;
    }
}
