#Ratchet

[![Build Status](https://secure.travis-ci.org/cboden/Ratchet.png?branch=master)](http://travis-ci.org/cboden/Ratchet)

A PHP 5.3 library for asynchronously serving WebSockets.
Build up your application through simple interfaces and re-use your application without changing any of its code just by combining different components. 

##WebSocket Compliance

* Supports the RFC6455, HyBi-10+, and Hixie76 protocol versions (at the same time)
* Tested on Chrome 13 - 27, Firefox 6 - 21, Safari 5.0.1 - 6, iOS 4.2 - 6
* Ratchet [passes](http://socketo.me/reports/ab/) the [Autobahn Testsuite](http://autobahn.ws/testsuite) (non-binary messages)

##Requirements

Shell access is required and root access is recommended.
To avoid proxy/firewall blockage it's recommended WebSockets are requested on port 80, which requires root access.
In order to do this, along with your sync web stack, you can either use a reverse proxy or two separate machines.
You can find more details in the [server conf docs](http://socketo.me/docs/deploy#serverconfiguration).

PHP 5.3.3 (or higher) is required. If you have access, PHP 5.4 is *highly* recommended for its performance improvements.

### Documentation

User and API documentation is available on Ratchet's website: http://socketo.me

See https://github.com/cboden/Ratchet-examples for some out-of-the-box working demos using Ratchet.

Need help?  Have a question?  Want to provide feedback?  Write a message on the [Google Groups Mailing List](https://groups.google.com/forum/#!forum/ratchet-php).

---

###A quick server example

```php
<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

    require __DIR__ . '/vendor/autoload.php';

/**
 * chat.php
 * Send any incoming messages to all connected clients (except sender)
 */
class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}

    // Run the server application through the WebSocket protocol on port 8080
    $server = IoServer::factory(new WsServer(new Chat), 8080);
    $server->run();
```

    $ php chat.php