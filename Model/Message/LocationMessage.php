<?php

namespace YPL\WechatSDK\Model\Message;


/**
 * LocationMessage
 *
 * @ORM\Entity
 */
class LocationMessage extends BaseMessage
{

    /**
     * @var float
     */
    private $locationX;

    /**
     * @var float
     */
    private $locationY;

    /**
     * @var integer
     */
    private $scale;

    /**
     * @var string
     */
    private $label;

    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['Location_X']) && $this->locationX = $rawMessage['Location_X'];
        isset($rawMessage['Location_Y']) && $this->locationY = $rawMessage['Location_Y'];
        isset($rawMessage['Scale']) && $this->scale = $rawMessage['Scale'];
        isset($rawMessage['Label']) && $this->label = $rawMessage['Label'];
    }

    public function validate()
    {
        return parent::validate() && $this->locationX && $this->locationY && $this->scale && $this->label;
    }

    /**
     * Set locationX
     *
     * @param float $locationX
     * @return LocationMessage
     */
    public function setLocationX($locationX)
    {
        $this->locationX = $locationX;

        return $this;
    }

    /**
     * Get locationX
     *
     * @return float
     */
    public function getLocationX()
    {
        return $this->locationX;
    }

    /**
     * Set locationY
     *
     * @param float $locationY
     * @return LocationMessage
     */
    public function setLocationY($locationY)
    {
        $this->locationY = $locationY;

        return $this;
    }

    /**
     * Get locationY
     *
     * @return float
     */
    public function getLocationY()
    {
        return $this->locationY;
    }

    /**
     * Set scale
     *
     * @param integer $scale
     * @return LocationMessage
     */
    public function setScale($scale)
    {
        $this->scale = $scale;

        return $this;
    }

    /**
     * Get scale
     *
     * @return integer
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Set Label
     *
     * @param string $locationLabel
     * @return LocationMessage
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get Label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
