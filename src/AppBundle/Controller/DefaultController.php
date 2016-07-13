<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller {

    /**
     * @Route("/", name="home")
     */
    public function indexAction() {
        /* todo render nice welcome page with informations about the app */
        return new RedirectResponse($this->get('router')->generate('time'));
    }

}
