<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * SubscribeEvent
 *
 */
class ScanEvent extends BaseEvent
{

    /**
     * @var string
     */
    private $eventKey;

    /**
     * @var string
     */
    private $ticket;


    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['EventKey']) && $this->eventKey = $rawMessage['EventKey'];
        isset($rawMessage['Ticket']) && $this->ticket = $rawMessage['Ticket'];
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

    /**
     * Set ticket
     *
     * @param string $ticket
     * @return SubscribeEvent
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return string
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}
