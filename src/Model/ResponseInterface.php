<?php
/*
 * This file is part of the Wechat SDK package.
 *
 * (c) yplam <yplam@yplam.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace YPL\WechatSDK\Model;

interface ResponseInterface
{
    /**
     * 返回数组格式的Response结构
     */
    public function getRawResponse();
}

