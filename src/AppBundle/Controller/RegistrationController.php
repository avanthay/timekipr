<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\Serializer\Exception\UnsupportedException;

class RegistrationController extends BaseController {

    public function registerAction() {
        throw new UnsupportedException('Self registration is currently not supported.');
    }
}
