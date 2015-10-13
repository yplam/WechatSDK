<?php
namespace YPL\WechatSDK\Tests;

use GuzzleHttp\Client;
use YPL\WechatSDK\Wechat;

class WechatTest extends \PHPUnit_Framework_TestCase
{
    function testWechatAPI()
    {
        $client = new Client();
        $wechat = new Wechat($client,array(
            'appid' => $GLOBALS['appid'],
            'appsecret' => $GLOBALS['appsecret'],
            'token' => $GLOBALS['token'],
            ));
        $accessToken = $wechat->getAccessToken();
        $this->assertTrue(is_string($accessToken));

        $menu = $wechat->getMenu();
        $this->assertTrue(is_array($menu));
        $this->assertTrue(isset($menu['menu']));

        $menu = array(
            'button' => array(
                array(
                    'type' => 'click',
                    'name' => 'WechatSDK',
                    'key'  => 'WechatSDK'
                )
            )

        );

        $menuStatus = $wechat->setMenu($menu);
        $this->assertEquals($menuStatus['errcode'], 0);

        $media = $wechat->uploadMedia(__DIR__ . '/plus.png', 'image');
        $this->assertTrue(is_array($media));
        $this->assertTrue(isset($media['type']) && $media['type'] == 'image');

        $image = $wechat->uploadImg(__DIR__ . '/plus.png');
        $this->assertTrue(is_array($image));
        $this->assertTrue(isset($image['url']));

        $imageMaterial = $wechat->addMaterial(__DIR__ . '/plus.png', 'image');
        $this->assertTrue(is_array($imageMaterial));
        $this->assertTrue(isset($imageMaterial['media_id']));

        $mp3Material = $wechat->addMaterial(__DIR__ . '/mpthreetest.mp3', 'voice');
        $this->assertTrue(is_array($mp3Material));
        $this->assertTrue(isset($mp3Material['media_id']));

        $mp4Material = $wechat->addMaterial(__DIR__ . '/SampleVideo_720x480_1mb.mp4', 'video', array(
            'title' => 'test wechat SDK',
            'description' => 'sample for test'
            ));
        $this->assertTrue(is_array($mp4Material));
        $this->assertTrue(isset($mp4Material['media_id']));
    }
}