<?php
/**
 * Created by PhpStorm.
 * User: cyrille
 * Date: 16/02/2018
 * Time: 12:45
 */

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\TestTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
class UserControllerTest extends WebTestCase
{
    use TestTrait;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testListAction()
    {
        //unauthenticated request
        $this->unauthRequest('/users');

        //authenticated but unauthorized request
        $this->logInAsUser();
        $crawler = $this->client->request('GET', '/users');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        //authenticated and authorized request
        $this->logInAsAdmin();
        $crawler = $this->client->request('GET', '/users');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Liste des utilisateurs")')->count());
    }

    public function testCreateAction()
    {
        //unauthenticated request
        $this->unauthRequest('/users/create');

        //authenticated but unauthorized request
        $this->logInAsUser();
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        //authenticated and authorized request
        $this->logInAsAdmin();
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Adresse email")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        //test new user with different password
        $form->setValues(['user[username]' => 'testUser', 'user[password][first]' => 'a', 'user[password][second]' => 'b']);
        $crawler = $this->client->submit($form);
        $this->assertSame(1, $crawler->filter('html:contains("Les deux mots de passe doivent correspondre.")')->count());

        //test new user correctly set
        $form = $crawler->selectButton('Ajouter')->form();
        $form->setValues(['user[username]' => 'testUser', 'user[password][first]' => 'a', 'user[password][second]' => 'a', 'user[email]' => 'test@gmail.com', 'user[roles]' => ['ROLE_USER']]);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        $this->deleteTestUser();
    }

    public function testEditUser()
    {
        $this->logInAsAdminWithUsername();
        $testUser = $this->em->getRepository('AppBundle:User')->findOneBy(['username' => 'testUser']);
        $testUserId = $testUser->getId();
        $crawler = $this->client->request('GET', 'users/'. $testUserId .'/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Adresse email")')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form->setValues(['user[username]' => 'testUser', 'user[password][first]' => 'b', 'user[password][second]' => 'b', 'user[email]' => 'test2@gmail.com', 'user[roles]' => ['ROLE_USER']]);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        $this->deleteTestUser();
    }
}