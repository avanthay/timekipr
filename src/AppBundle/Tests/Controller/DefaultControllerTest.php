<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();

        $client->request('GET', '/');

        /* client should be redirected to the login screen */
        $this->assertTrue($client->getResponse()->isRedirect());
    }
}
