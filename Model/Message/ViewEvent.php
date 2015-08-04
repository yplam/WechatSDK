<?php

namespace YPL\WechatSDK\Model\Event;


/**
 * ViewEvent
 *
 */
class ViewEvent extends BaseEvent
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

    /**
     * Set eventKey
     *
     * @param string $eventKey
     * @return ViewEvent
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
