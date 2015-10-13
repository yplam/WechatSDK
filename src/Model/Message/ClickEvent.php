<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * ClickEvent
 *
 */
class ClickEvent extends BaseEvent
{

    /**
     * @var string
     */
    private $eventKey;


    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['EventKey']) && $this->eventKey = $rawMessage['EventKey'];
    }

    public function validate()
    {
        return parent::validate() && $this->eventKey;
    }

    /**
     * Set eventKey
     *
     * @param string $eventKey
     * @return SubscribeEvent
     */
    public function setEventKey($eventKey)
    {
        $this->eventKey = $eventKey;

        return $this;
    }

    /**
     * Get eventKey
     *
     * @return string
     */
    public function getEventKey()
    {
        return $this->eventKey;
    }


}
