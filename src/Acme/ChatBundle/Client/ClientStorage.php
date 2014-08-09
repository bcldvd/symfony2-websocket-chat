<?php

namespace Acme\ChatBundle\Client;

use Acme\ChatBundle\Client\Client;
use Ratchet\ConnectionInterface as Conn;


class ClientStorage {

    /**
     * @var array $clientStorage
     */
    private $clientStorage;

    function __construct()
    {
        $this->clientStorage = array();
    }

    /**
     * Get a specific client with his $conn
     *
     * @param Conn $conn
     * @return mixed
     */
    public function getClient(Conn $conn)
    {
        $id = $conn->resourceId;
        if ($this->hasClient($id)) {
            return $this->clientStorage[$id];
        } else {
            return false;
        }
    }

    /**
     * Get a specific client with his name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getClientByName($name)
    {
        foreach($this->clientStorage as $client)
        {
            if ($client->getName() === $name)
            {
                return $client;
            }
        }
        return false;
    }

    /**
     * Add a client to client storage
     *
     * @param Conn $conn
     * @param string $name
     *
     * @return bool
     */
    public function addClient(Conn $conn, $name)
    {
        $id = $conn->resourceId;
        if (!$this->hasClient($id)) {
            $client = new Client($conn, $name);
            $this->clientStorage[$id] = $client;
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Check if a client is present in storage
     *
     * @param $id
     * @return bool
     */
    public function hasClient($id)
    {
        return array_key_exists($id, $this->clientStorage);
    }

    public function removeClient(Conn $conn)
    {
        $id = $conn->resourceId;
        if (!$this->hasClient($id)) {
            unset($this->clientStorage[$id]);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all clients stored in storage
     *
     * @return array
     */
    public function getAllClients()
    {
        return $this->clientStorage;
    }


} 