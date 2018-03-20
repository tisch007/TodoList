<?php
/**
 * Created by PhpStorm.
 * User: cyrille
 * Date: 29/01/2018
 * Time: 17:34
 */

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
class DefaultControllerTest extends WebTestCase
{
    use TestTrait;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testIndexAction()
    {
        //unauthenticated request
        $this->unauthRequest('/');

        //authenticated as user
        $this->logInAsUser();
        $crawler = $this->client->request('GET', '/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());

        //authenticated as admin
        $this->logInAsAdmin();
        $crawler = $this->client->request('GET', '/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());
    }
}