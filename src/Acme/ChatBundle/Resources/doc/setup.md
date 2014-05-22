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
    bundles:        [ JDareClankBundle ]
```

### Configure WebSocket Server

Add the following to your app/config.yml

```yaml
# Clank Configuration
clank:
    web_socket_server:
        port: 8080        #The port the socket server will listen on
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
