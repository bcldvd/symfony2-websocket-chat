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
