<?php
/**
 * Created by PhpStorm.
 * User: cyrille
 * Date: 19/02/2018
 * Time: 17:15
 */

namespace Tests\AppBundle\Controller;


use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\TestTrait;
use Symfony\Component\HttpFoundation\Response;
class TaskControllerTest extends WebTestCase
{
    use TestTrait;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testListAction()
    {
        //unauthenticated request
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('form.loginForm')->count());

        //authenticated as User
        $this->logInAsUserWithUsername();
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches terminées")')->count());
        $this->deleteTestUser($this->em);

        //authenticated as Admin
        $this->logInAsAdminWithUsername();
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches terminées")')->count());
        $this->deleteTestUser($this->em);
    }

    public function testListActionDone()
    {
        //unauthenticated request
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('form.loginForm')->count());

        //authenticated as User
        $this->logInAsUserWithUsername();
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches à faire")')->count());
        $this->deleteTestUser($this->em);

        //authenticated as Admin
        $this->logInAsAdminWithUsername();
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches à faire")')->count());
        $this->deleteTestUser($this->em);
    }
/*
    public function testCreateAction()
    {
        //unauthenticated request
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('form.loginForm')->count());

        //authenticated as User
        $this->logInAsUserWithUsername();
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Retour à la liste des tâches")')->count());
        $this->deleteTestUser($this->em);

        $form = $crawler->selectButton('Ajouter')->form();
        $form->setValues(['task[title]' => 'testTask', 'task[content]' => 'contenue de la tâche de test']);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();


        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        var_dump($crawler);
        //$this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
    }
*/
    public function testToggleTaskAction()
    {
        //unauthenticated request
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('form.loginForm')->count());

        //authenticated as User
        $this->logInAsUserWithUsername();
        $this->createTask();

        $testTask = $this->em->getRepository('AppBundle:Task')->findOneByTitle('testTask');
        $TestTaskId = $testTask->getId();
        //test task done
        $this->client->request('GET', 'tasks/'. $TestTaskId .'/toggle');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSame(1, $crawler->filter('html:contains("est marquée comme faite")')->count());
        //test task not done
        $this->client->request('GET', 'tasks/'. $TestTaskId .'/toggle');
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->assertSame(1, $crawler->filter('html:contains("est marquée comme à faire")')->count());

        $this->deleteTestUser($this->em);
        $this->deleteTestTask($this->em);
    }
}