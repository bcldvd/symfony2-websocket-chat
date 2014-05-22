#Topic Handler Setup

Although the standard Clank PubSub can be useful as a simple channel for allowing messages to be pushed to users, anymore advanced functionality will require custom Topic Handlers.

Similar to RPC, topic handlers are slightly specialised Symfony2 services. They must implement the interface "JDare\ClankBundle\Topic\TopicInterface"

##Step 1: Create the Topic Handler Service Class

```php
<?php

namespace Acme\HelloBundle\Topic;

use JDare\ClankBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface as Conn;

class AcmeTopic implements TopicInterface
{

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onSubscribe(Conn $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast($conn->resourceId . " has joined " . $topic->getId());
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @return void
     */
    public function onUnSubscribe(Conn $conn, $topic)
    {
        //this will broadcast the message to ALL subscribers of this topic.
        $topic->broadcast($conn->resourceId . " has left " . $topic->getId());
    }


    /**
     * This will receive any Publish requests for this topic.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
        /*
        $topic->getId() will contain the FULL requested uri, so you can proceed based on that

        e.g.

        if ($topic->getId() == "acme/channel/shout")
            //shout something to all subs.
        */


        $topic->broadcast(array(
            "sender" => $conn->resourceId,
            "topic" => $topic->getId(),
            "event" => $event
        ));
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
 <service id="acme_hello.topic_sample_service" class="Acme\HelloBundle\Topic\AcmeTopic" />
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
            service: "acme_hello.topic_sample_service" #The service id.
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

For more information on the Client Side of Clank, please see [Client Side Setup](ClientSetup.md)
