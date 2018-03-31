<?php
/**
 * Created by PhpStorm.
 * User: cyrille
 * Date: 16/02/2018
 * Time: 10:40
 */

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use TestTrait;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testLoginAction()
    {
        //unauthenticated request
        $crawler = $this->client->request('GET', '/login');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('form.loginForm')->count());

        // test with bad credentials
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues(['_username' => 'badUser', '_password' => 'badPassword']);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Invalid credentials.")')->count());

        // test with good credentials
        $form = $crawler->selectButton('Se connecter')->form();
        $form->setValues(['_username' => 'tisch' , '_password' => 'a']);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('html:contains("Bienvenue sur Todo List")')->count());
    }

    public function testLogoutCheck()
    {
        //test logout as user
        $this->logInAsUser();
        $crawler = $this->client->request('GET', '/logout');
        $this->client->followRedirect();
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertSame('http://localhost/login', $this->client->getResponse()->getTargetUrl());

        //test logout as admin
        $this->logInAsAdmin();
        $crawler = $this->client->request('GET', '/logout');
        $this->client->followRedirect();
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertSame('http://localhost/login', $this->client->getResponse()->getTargetUrl());
    }
}
