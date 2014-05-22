# Setup

This document will describe what I have accomplished here to integrate Clank and make a complete chat app.

A lot of the steps are taken from [ClankBundle's docs](https://github.com/JDare/ClankBundle/tree/master/Resources/docs)


### Install via composer

Add the following to your composer.json

```javascript
{
    "require": {
        "jdare/clank-bundle": "0.1.*"
    }
}
```

Then update composer to install the new packages:
```command
php composer.phar update
```

### Add to your App Kernel

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new JDare\ClankBundle\JDareClankBundle(),
    );
}
```

### Add to Assetic Bundles

Add "JDareClankBundle" to your assetic bundles in app/config (this is required to render the client side code).

```yaml
# Assetic Configuration
assetic:
    ...
    bundles:
        - AcmeChatBundle
        - JDareClankBundle
```

### Configure WebSocket Server

Add the following to your app/config.yml

```yaml
# Clank Configuration
clank:
    web_socket_server:
        port: 1337        #The port the socket server will listen on
        host: 127.0.0.1   #(optional) The host ip to bind to
```

_Note: when connecting on the client, if possible use the same values as here to ensure compatibility for sessions etc._

### Launching the Server

The Server Side Clank installation is now complete. You should be able to run this from the root of your symfony installation.

```command
php app/console clank:server
```

If everything is successful, you will see something similar to the following:

```
Starting Clank
Launching Ratchet WS Server on: *:8080
```

This means the websocket server is now up and running!

### Include clank's Javascript

To include the relevant javascript libraries necessary for Clank, add these to your root layout file just before the closing body tag.

```twig
...

{{ clank_client() }}
</body>
```
_Note: This requires assetic and twig to be installed_

If you are NOT using twig as a templating engine, you will need to include the following javascript files from the bundle:

```javascript
JDareClankBundle/Resources/public/js/clank.js
JDareClankBundle/Resources/public/js/vendor/autobahn.min.js
```

If using the {{ clank_client }} method, please ensure to run the following when using the production environment:

```command
php app/console assetic:dump --env=prod --no-debug
```

This is to ensure the client js libraries are available.

### Add to twig for client side access

So the client side templating can access those same parameters, add this to your "app/config/config.yml"

```yaml
twig:
    ...
    globals:
        clank_host:    127.0.0.1
        clank_port:    1337
```

Note : You could use parameters like "%clank_host%" and "%clank_port%" by modifying your "app/config/parameters.yml" but with my configuration, composer keeps deleting them :/

### Render in template

In your root twig layout template, add the following

```html
<script type="text/javascript">
    var _CLANK_URI = "ws://{{ clank_host }}:{{ clank_port }}";
</script>
```

Now you will have access to a variable "_CLANK_URI" which you can connect with:

```javascript
var myClank = Clank.connect(_CLANK_URI);
```

Alternatively, if you don't like polluting your global scope, you can render it directly into your javascript file by processing it via a controller.


Now from there, if you have a clank server up and running in your console, you can reload your web page and see a new user connecting in your console

```
639 connected
```

#Topic Handler Setup

Although the standard Clank PubSub can be useful as a simple channel for allowing messages to be pushed to users, anymore advanced functionality will require custom Topic Handlers.

Similar to RPC, topic handlers are slightly specialised Symfony2 services. They must implement the interface "JDare\ClankBundle\Topic\TopicInterface"

##Step 1: Create the Topic Handler Service Class

```php
<?php

<?php

namespace JDare\ClankChatBundle\Topic;

use Ratchet\ConnectionInterface as Conn;

use JDare\ClankBundle\Server\App\Handler\TopicHandlerInterface;

class ChatTopic implements TopicHandlerInterface
{
    /**
     * Announce to this topic that someone else has joined the chat room
     *
     * Also, set their nickname to Guest if it doesnt exist.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     */
    public function onSubscribe(Conn $conn, $topic)
    {
        if (!isset($conn->ChatNickname))
        {
            $conn->ChatNickname = "Guest";
        }

        $msg = $conn->ChatNickname . " joined the chat room.";

        $topic->broadcast(array("msg" => $msg, "from" => "System"));
    }

    /**
     * Announce person left chat room
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     */
    public function onUnSubscribe(Conn $conn, $topic)
    {
        $msg = $conn->ChatNickname . " left the chat room.";

        $topic->broadcast(array("msg" => $msg, "from" => "System"));
    }

    /**
     * Do some nice things like strip html/xss etc. then pass along the message
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
        $event = htmlentities($event); // removing html/js

        $topic->broadcast(array("msg" => $event, "from" => $conn->ChatNickname));
    }
}
```

The 3 methods that must be implemented are:

* onSubscribe(Conn $conn, $topic)
* onUnSubscribe(Conn $conn, $topic)
* onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)

$conn is the connection object of the client who has initiated this event.

$topic is the [Topic object](http://socketo.me/api/class-Ratchet.Wamp.Topic.html). This also contains a list of current subscribers, so you don't have to manually keep track.

##Step 2: Register your service with Symfony

If you are using XML, edit "YourBundle/Resources/config/services.xml", add:

```xml
 <service id="acme_chat.chat_topic_handler" class="Acme\ChatBundle\Topic\ChatTopic" />
 ```

For other formats, please check the [Symfony2 Documents](http://symfony.com/doc/master/book/service_container.html)

##Step 3: Register your service with Clank

Open your "app/config/config.yml" and append the following:

```yaml
clank:
    ...
    topic:
        -
            name: "acme" #Important! this is the topic namespace used to match to this service!
            service: "acme_chat.chat_topic_handler" #The service id.
    # Add as many as you need
    #    -
    #        name: "other"
    #        service: "acme_hello.topic_other_service"
```

The name parameter for each class will match the network namespace for listening to events on this topic.

The following javascript will show connecting to this topic, notice how "acme/channel" will match the name "acme" we gave the service.

```javascript
    ...

    //the callback function in "subscribe" is called everytime an event is published in that channel.
    session.subscribe("acme/channel", function(uri, payload){
        console.log("Received message", payload.msg);
    });

    session.publish("acme/channel", {msg: "This is a message!"});

    session.unsubscribe("acme/channel");

    session.publish("acme/channel", {msg: "I won't see this"});
```

If we wanted to include dynamic channels based on that namespace, we could include them, and they will all get routed through that Topic Handler.

```javascript
    ...

    //the callback function in "subscribe" is called everytime an event is published in that channel.
    session.subscribe("acme/channel/id/12345", function(uri, payload){
        console.log("Received message", payload.msg);
    });

    session.publish("acme/channel/id/12345", {msg: "This is a message!"});
```
_Please note, this is not secure as anyone can subscribe to these channels by making a request for them. For true private channels, you will need to implement server side security_
