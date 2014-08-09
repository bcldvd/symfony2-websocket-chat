<?php

namespace Acme\ChatBundle\RPC;

use Acme\ChatBundle\Client\ClientStorage;
use Ratchet\ConnectionInterface as Conn;

class ChatService
{
    /**
     * @var ClientStorage $clientStorage
     */
    private $clientStorage;

    function __construct(ClientStorage $clientStorage)
    {
        $this->clientStorage = $clientStorage;
    }

    /**
     * Changes the nickname of current user
     *
     * @param Conn $conn
     * @param $params
     *
     * @return bool
     */
    public function changeNickname(Conn $conn, $params)
    {
        if (!isset($params['nickname'])) { return false;}

        $client = $this->clientStorage->getClient($conn);
        $client->setName(htmlentities($params['nickname']));

        return true; //this sends a positive result back to the client
    }

    /**
     * Changes the status of current user
     *
     * @param Conn $conn
     * @param $params
     *
     * @return bool
     */
    public function changeStatus(Conn $conn, $params)
    {
        if (!isset($params['status'])) { return false;}

        $client = $this->clientStorage->getClient($conn);
        $client->setStatus($params['status']);

        return true;
    }

    /**
     * Get statuses of given users
     *
     * @param Conn $conn
     * @param $params
     *
     * @return bool
     */
    public function getStatuses(Conn $conn, $params)
    {
        if (!isset($params['users'])) { return false;}

        $statuses = [];

        foreach ($params as $username)
        {
            $client = $this->clientStorage->getClientByName($username);

            if ($client) {
                $status = array(
                    'user' => $username,
                    'status' => $client->getStatus()
                );
                array_push($statuses, $status);
            }
        }
        $conn->send(json_encode($statuses));

        return true;
    }
}
