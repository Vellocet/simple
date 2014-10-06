<?php

namespace Vellocet\SimpleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VellocetSimpleBundle:Default:index.html.twig');
    }
}
