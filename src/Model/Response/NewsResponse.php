<?php

namespace YPL\WechatSDK\Model\Response;


/**
 * NewsResponse
 *  <xml>
    <ToUserName><![CDATA[toUser]]></ToUserName>
    <FromUserName><![CDATA[fromUser]]></FromUserName>
    <CreateTime>12345678</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>2</ArticleCount>
    <Articles>
    <item>
    <Title><![CDATA[title1]]></Title> 
    <Description><![CDATA[description1]]></Description>
    <PicUrl><![CDATA[picurl]]></PicUrl>
    <Url><![CDATA[url]]></Url>
    </item>
    <item>
    <Title><![CDATA[title]]></Title>
    <Description><![CDATA[description]]></Description>
    <PicUrl><![CDATA[picurl]]></PicUrl>
    <Url><![CDATA[url]]></Url>
    </item>
    </Articles>
    </xml> 
 */
class NewsResponse extends BaseResponse
{


    /**
     * @var array
     *
     */
    private $articles;


    public function __construct(array $rawResponse = array())
    {
        parent::__construct($rawResponse);
        $this->articles = array();
        if( !empty($rawResponse['Articles']) && is_array($rawResponse['Articles'])){
            foreach($rawResponse['Articles'] as $item){
                $article = array();
                $article['Title'] = isset($item['Title']) ? $item['Title'] : '';
                $article['Description'] = isset($item['Description']) ? $item['Description'] : '';
                $article['PicUrl'] = isset($item['PicUrl']) ? $item['PicUrl'] : '';
                $article['Url'] = isset($item['Url']) ? $item['Url'] : '';
                $this->articles[] = $article;
            }
        }
    }

    public function getRawResponse()
    {
        $rawResponse = parent::getRawResponse();
        $rawResponse['MsgType'] = 'news';
        $rawResponse['ArticleCount'] = count($this->articles);
        $rawResponse['Articles'] = array();
        foreach($this->articles as $item){
            $article = array();
            $articleItem = array();
            $articleItem['Title'] = isset($item['Title']) ? $item['Title'] : '';
            $articleItem['Description'] = isset($item['Description']) ? $item['Description'] : '';
            $articleItem['PicUrl'] = isset($item['PicUrl']) ? $item['PicUrl'] : '';
            $articleItem['Url'] = isset($item['Url']) ? $item['Url'] : '';
            $article['item'] = $articleItem;
            $rawResponse['Articles'][] = $article;
        }
        return $rawResponse;
    }


    /**
     * Set articles
     *
     * @param array $articles
     * @return NewsResponse
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * Get articles
     *
     * @return array 
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
