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

interface MessageManagerInterface
{
    public function createFromRawMessage(array $rawMessage);
    public function validate(MessageInterface $message);
}
