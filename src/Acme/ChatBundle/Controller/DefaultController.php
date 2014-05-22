<?php

namespace Acme\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AcmeChatBundle:Default:index.html.twig');
    }

    public function aboutAction()
    {
        return $this->render('AcmeChatBundle:Default:about.html.twig');
    }
}
