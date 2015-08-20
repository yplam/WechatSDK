<?php
/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) yplam <yplam@yplam.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace YPL\WechatSDK\Model;

interface MessageInterface
{
    public function validate();
    public function getFromUserName();
    public function getToUserName();
}

