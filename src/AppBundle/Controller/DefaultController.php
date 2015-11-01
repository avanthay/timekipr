<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="app")
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render('::index.html.twig');
    }

    /**
     * @Route("/admin/employee", name="employee")
     */
    public function adminEmployeeAction() {
        return $this->render(':admin:employee_overview.html.twig');
    }
}
