<?php

namespace Acme\ChatBundle\Client;

use Ratchet\ConnectionInterface as Conn;
use Acme\ChatBundle\Client\Status;

class Client {

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var \Ratchet\ConnectionInterface
     */
    private $conn;

    /**
     * @var Status $status
     */
    private $status;

    function __construct(Conn $conn, $name)
    {
        $this->conn = $conn;
        $this->name = $name;
        $this->status = new Status();
    }

    /**
     * @return \Ratchet\ConnectionInterface
     */
    public function getConn()
    {
        return $this->conn;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status->getStatus();
    }

    /**
     * @param $status
     *
     * @return bool
     */
    public function setStatus($status)
    {
        return $this->status->setStatus($status);
    }

} 