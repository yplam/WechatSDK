<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * TextMessage
 *
 */
class TextMessage extends BaseMessage
{

    /**
     * @var string
     */
    private $content;

    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['Content']) && $this->content = $rawMessage['Content'];
    }
    /**
     * Set content
     *
     * @param string $content
     * @return TextMessage
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
