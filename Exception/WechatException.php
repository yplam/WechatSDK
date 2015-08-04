<?php

namespace YPL\WechatSDK\Exception;


/**
 * API出错信息
 */
class WechatException extends \Exception
{
    /**
     * @var string
     */
    protected $errorCode;


    /**
     */
    public function __construct($error, $errorCode = 1)
    {
        parent::__construct($error);

        $this->errorCode = $errorCode;
    }

    /**
     * Get error description
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

}
