<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * ShortVideoMessage
 *
 */
class ShortVideoMessage extends BaseMessage
{

    /**
     * @var integer
     */
    private $mediaId;

    /**
     * @var integer
     */
    private $thumbMediaId;

    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['MediaId']) && $this->mediaId = $rawMessage['MediaId'];
        isset($rawMessage['ThumbMediaId']) && $this->thumbMediaId = $rawMessage['ThumbMediaId'];
    }

    /**
     * Set mediaId
     *
     * @param integer $mediaId
     * @return ShortVideoMessage
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

    /**
     * Set thumbMediaId
     *
     * @param integer $thumbMediaId
     * @return ShortVideoMessage
     */
    public function setThumbMediaId($thumbMediaId)
    {
        $this->thumbMediaId = $thumbMediaId;

        return $this;
    }

    /**
     * Get thumbMediaId
     *
     * @return integer
     */
    public function getThumbMediaId()
    {
        return $this->thumbMediaId;
    }
}
