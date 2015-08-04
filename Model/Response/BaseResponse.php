<?php

namespace YPL\WechatSDK\Model\Response;

use YPL\WechatSDK\Model\ResponseInterface;
/**
 * BaseResponse
 *
 */
class BaseResponse implements ResponseInterface
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


    public function __construct(array $rawResponse = array())
    {
        isset($rawResponse['ToUserName']) && $this->toUserName = $rawResponse['ToUserName'];
        isset($rawResponse['FromUserName']) && $this->fromUserName = $rawResponse['FromUserName'];
        if(isset($rawResponse['CreateTime'])){
            $this->createtime = (int)$rawResponse['CreateTime'];
        }
        else{
            $this->createtime = time();
        }
    }

    public function getRawResponse()
    {
        return array(
            'ToUserName' => $this->toUserName,
            'FromUserName' => $this->fromUserName,
            'CreateTime' => $this->createtime,
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

}
