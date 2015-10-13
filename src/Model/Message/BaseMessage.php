<?php

namespace YPL\WechatSDK\Model\Message;

use YPL\WechatSDK\Model\MessageInterface;
/**
 * BaseMessage
 *
 */
class BaseMessage implements MessageInterface
{

    /**
     * @var string
     */
    private $toUserName;

    /**
     * @var string
     */
    private $fromUserName;

    /**
     * @var integer
     */
    private $createtime;

    /**
     * @var integer
     */
    private $msgId;

    /**
     * @var string
     */
    private $msgType;

    public function __construct(array $rawMessage = array())
    {
        isset($rawMessage['ToUserName']) && $this->toUserName = $rawMessage['ToUserName'];
        isset($rawMessage['FromUserName']) && $this->fromUserName = $rawMessage['FromUserName'];
        isset($rawMessage['CreateTime']) && $this->createtime = (int)$rawMessage['CreateTime'];
        isset($rawMessage['MsgId']) && $this->msgId = $rawMessage['MsgId'];
        isset($rawMessage['MsgType']) && $this->msgType = $rawMessage['MsgType'];
    }

    public function validate()
    {
        return ( $this->toUserName 
            && $this->fromUserName
            && $this->createtime
            && $this->msgId
            && $this->msgType
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
     * Set msgId
     *
     * @param integer $msgId
     * @return BaseMessage
     */
    public function setMsgId($msgId)
    {
        $this->msgId = $msgId;

        return $this;
    }

    /**
     * Get msgId
     *
     * @return integer
     */
    public function getMsgId()
    {
        return $this->msgId;
    }

    /**
     * Get msgType
     *
     * @return string
     */
    public function getMsgType()
    {
        return $this->msgType;
    }
}
