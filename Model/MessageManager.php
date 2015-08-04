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

class MessageManager implements MessageManagerInterface
{

    protected $messageTypes = array(
        'text' => 'YPL\WechatSDK\Model\Message\TextMessage',
        'image' => 'YPL\WechatSDK\Model\Message\ImageMessage',
        'voice' => 'YPL\WechatSDK\Model\Message\VoiceMessage',
        'video' => 'YPL\WechatSDK\Model\Message\VideoMessage',
        'shortvideo' => 'YPL\WechatSDK\Model\Message\ShortvideoMessage',
        'location' => 'YPL\WechatSDK\Model\Message\LocationMessage',
        'link' => 'YPL\WechatSDK\Model\Message\LinkMessage',
        // event
        'subscribe' => 'YPL\WechatSDK\Model\Message\SubscribeEvent',
        'unsubscribe' => 'YPL\WechatSDK\Model\Message\UnsubscribeEvent',
        'SCAN' => 'YPL\WechatSDK\Model\Message\ScanEvent',
        'LOCATION' => 'YPL\WechatSDK\Model\Message\LocationEvent',
        'CLICK' => 'YPL\WechatSDK\Model\Message\ClickEvent',
        'VIEW' => 'YPL\WechatSDK\Model\Message\ViewEvent',
    );

    public function createFromRawMessage(array $rawMessage)
    {
        if(empty($rawMessage['MsgType'])){
            return false;
        }
        if($rawMessage['MsgType'] == 'event'){
            $msgType = isset($rawMessage['Event']) ? $rawMessage['Event'] : 'event' ;
        }
        else{
            $msgType = $rawMessage['MsgType'];
        }

        if(!in_array($msgType, array_keys($this->messageTypes))){
                return false;
        }
        $messageClass = $this->messageTypes[$msgType];
        if(!class_exists($messageClass)){
            return false;
        }
        $message = new $messageClass($rawMessage);
        return $message;
    }

    public function validate($message)
    {

    }
}

