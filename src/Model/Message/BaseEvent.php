<?php

namespace YPL\WechatSDK\Model\Message;

use YPL\WechatSDK\Model\MessageInterface;
/**
 * BaseEvent
 *
 */
class BaseEvent implements MessageInterface
{

    /**
     * @var string
     */
    protected $toUserName;

    /**
     * @var string
     */
    protected $fromUserName;

    /**
     * @var integer
     */
    protected $createtime;

    /**
     * @var string
     */
    protected $event;


    public function __construct(array $rawMessage = array())
    {
        isset($rawMessage['ToUserName']) && $this->toUserName = $rawMessage['ToUserName'];
        isset($rawMessage['FromUserName']) && $this->fromUserName = $rawMessage['FromUserName'];
        isset($rawMessage['CreateTime']) && $this->createtime = (int)$rawMessage['CreateTime'];
        isset($rawMessage['Event']) && $this->event = $rawMessage['Event'];
    }

    public function validate()
    {
        return ( $this->toUserName
            && $this->fromUserName
            && $this->createtime
            && $this->event
        );
    }

    /**
     * Set toUserName
     *
     * @param string $toUserName
     * @return BaseMessage
     */
    public function setToUserName($toUserName)
    {
        $this->toUserName = $toUserName;

        return $this;
    }

    /**
     * Get toUserName
     *
     * @return string
     */
    public function getToUserName()
    {
        return $this->toUserName;
    }

    /**
     * Set fromUserName
     *
     * @param string $fromUserName
     * @return BaseMessage
     */
    public function setFromUserName($fromUserName)
    {
        $this->fromUserName = $fromUserName;

        return $this;
    }

    /**
     * Get fromUserName
     *
     * @return string
     */
    public function getFromUserName()
    {
        return $this->fromUserName;
    }

    /**
     * Set createtime
     *
     * @param integer $createtime
     * @return BaseMessage
     */
    public function setCreatetime($createtime)
    {
        $this->createtime = $createtime;

        return $this;
    }

    /**
     * Get createtime
     *
     * @return integer
     */
    public function getCreatetime()
    {
        return $this->createtime;
    }

    /**
     * Set event
     *
     * @param string $event
     * @return BaseMessage
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

}
