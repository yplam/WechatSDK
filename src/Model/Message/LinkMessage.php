<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * LinkMessage
 *
 */
class LinkMessage extends BaseMessage
{

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $url;

    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['Title']) && $this->title = $rawMessage['Title'];
        isset($rawMessage['Description']) && $this->description = $rawMessage['Description'];
        isset($rawMessage['Url']) && $this->url = $rawMessage['Url'];
    }

    public function validate()
    {
        return parent::validate() && $this->title && $this->description && $this->url;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return LinkMessage
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return LinkMessage
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return LinkMessage
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
