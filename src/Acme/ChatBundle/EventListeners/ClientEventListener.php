<?php

namespace Acme\ChatBundle\EventListeners;

use Acme\ChatBundle\Client\ClientStorage;
use JDare\ClankBundle\Event\ClientEvent;
use JDare\ClankBundle\Event\ClientErrorEvent;

class ClientEventListener {
    /**
     * @var ClientStorage $clientStorage
     */
    private $clientStorage;


    function __construct(ClientStorage $clientStorage)
    {
        $this->clientStorage = $clientStorage;
    }


    /**
     * Called whenever a client connects
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        // Get security context from Session
        $security_context = unserialize($conn->Session->get('_security_main'));

        // If security context is not present, close connection
        if (!$security_context) {
            echo 'Client not authenticated, closing $conn' . PHP_EOL;
            $conn->close();
            return;
        } else {
            // Else, create new client
            $username = $security_context->getUsername();
            $this->clientStorage->addClient($conn, $username);
            echo $conn->resourceId . " is now authenticated as " . $username . PHP_EOL;

/*
            // Example Code

            var_dump($this->clientStorage->getClient($conn->resourceId)->getStatus());
            foreach($this->clientStorage->getAllClients() as $client)
            {
                var_dump($client->setStatus('busy'));
            }
            var_dump($this->clientStorage->getClient($conn->resourceId)->getStatus());*/

        }
    }

    /**
     * Called whenever a client disconnects
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        // Remove client from ClientStorage on disconnect
        $this->clientStorage->removeClient($conn);

        echo $conn->resourceId . " disconnected" . PHP_EOL;
    }

    /**
     * Called whenever a client errors
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e = $event->getException();

        echo "connection error occurred: " . $e->getMessage() . PHP_EOL;
    }
} 