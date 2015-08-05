<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * VoiceMessage
 *
 */
class VoiceMessage extends BaseMessage
{


    /**
     * @var integer
     */
    private $mediaId;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $recognition;


    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        $this->recognition = '';
        isset($rawMessage['MediaId']) && $this->mediaId = $rawMessage['MediaId'];
        isset($rawMessage['Format']) && $this->format = $rawMessage['Format'];
        isset($rawMessage['Recognition']) && $this->recognition = $rawMessage['Recognition'];
    }

    public function validate()
    {
        return parent::validate() && $this->mediaId && $this->format;
    }

    /**
     * Set mediaId
     *
     * @param integer $mediaId
     * @return VoiceMessage
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
     * Set format
     *
     * @param string $format
     * @return VoiceMessage
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set recognition
     *
     * @param string $recognition
     * @return VoiceMessage
     */
    public function setRecognition($recognition)
    {
        $this->recognition = $recognition;

        return $this;
    }

    /**
     * Get recognition
     *
     * @return string
     */
    public function getRecognition()
    {
        return $this->recognition;
    }
}
