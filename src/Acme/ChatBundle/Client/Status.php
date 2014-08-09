<?php

namespace Acme\ChatBundle\Client;


class Status {

    /**
     * @var string $status
     */
    private $status;

    /**
     * @var array $statusesArray
     */
    private $statusesArray = array(
        'online' => 'Online',
        'busy' => 'Busy',
        'offline' => 'Offline'
    );

    function __construct()
    {
        $this->status = $this->statusesArray['online'];
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    public function setStatus($status)
    {
        if (array_key_exists($status, $this->statusesArray))
        {
            $this->status = $this->statusesArray[$status];
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

} 