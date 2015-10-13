<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * ImageMessage
 *
 */
class ImageMessage extends BaseMessage
{

    /**
     * @var string
     */
    private $picUrl;

    /**
     * @var integer
     */
    private $mediaId;

    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['PicUrl']) && $this->picUrl = $rawMessage['PicUrl'];
        isset($rawMessage['MediaId']) && $this->mediaId = $rawMessage['MediaId'];
    }

    /**
     * Set picUrl
     *
     * @param string $picUrl
     * @return ImageMessage
     */
    public function setPicUrl($picUrl)
    {
        $this->picUrl = $picUrl;

        return $this;
    }

    /**
     * Get picUrl
     *
     * @return string
     */
    public function getPicUrl()
    {
        return $this->picUrl;
    }

    /**
     * Set mediaId
     *
     * @param integer $mediaId
     * @return ImageMessage
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * Get mediaId
     *
     * @return integer
     */
    public function getMediaId()
    {
        return $this->mediaId;
    }
}
