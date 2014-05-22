# Symfony2 Chat with Websockets


About
--------------
This project uses the [ClankBundle](https://github.com/JDare/ClankBundle) which is powered by : [Ratchet](http://socketo.me) and [Autobahn JS](http://autobahn.ws/js), with [Symfony2](http://symfony.com/)


## Getting Started

You should be able to run this from the root of the git clone of this project.

```command
php app/console clank:server
```

If everything is successful, you will see something similar to the following:

```
Starting Clank
Launching Ratchet WS Server on: *:8080
```

This means the websocket server is now up and running!

Check your config at http://YOURLOCALHOST/symfony2-websocket-chat/web/config.php
If everything looks good, then go to http://YOURLOCALHOST/symfony2-websocket-chat/web/app_dev.php


## Contributing
If something is unclear, confusing, or needs to be refactored, please let me know. Please open an issue before submitting a pull request

## Authors

**David Boclé** - http://github.com/bcldvd


## Credits

- [ClankBundle](https://github.com/JDare/ClankBundle) by [@JDare](https://github.com/JDare)
- [Ratchet](https://github.com/cboden/Ratchet) by [@cboden](https://github.com/cboden)
- [AutobahnJS](https://github.com/tavendo/AutobahnJS) by [@tavendo](https://github.com/tavendo)
- [Symfony](https://github.com/symfony/symfony) by [@Symfony](https://github.com/symfony)


## Copyright and license

    The MIT License

    Copyright (c) 2014 David Boclé

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
