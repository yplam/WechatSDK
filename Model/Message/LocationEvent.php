<?php

namespace YPL\WechatSDK\Model\Event;


/**
 * LocationEvent
 *
 */
class LocationEvent extends BaseEvent
{

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var float
     */
    private $precision;


    public function __construct(array $rawMessage = array())
    {
        parent::__construct($rawMessage);
        isset($rawMessage['Latitude']) && $this->latitude = $rawMessage['Latitude'];
        isset($rawMessage['Longitude']) && $this->longitude = $rawMessage['Longitude'];
        isset($rawMessage['Precision']) && $this->precision = $rawMessage['Precision'];
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return LocationEvent
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return LocationEvent
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set precision
     *
     * @param float $precision
     * @return LocationEvent
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Get precision
     *
     * @return float
     */
    public function getPrecision()
    {
        return $this->precision;
    }
}
