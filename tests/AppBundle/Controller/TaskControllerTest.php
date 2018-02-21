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
        $this->unauthRequest('/tasks');

        //authenticated as User
        $this->logInAsUserWithUsername();
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches terminées")')->count());
        $this->deleteTestUser();

        //authenticated as Admin
        $this->logInAsAdminWithUsername();
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches terminées")')->count());
        $this->deleteTestUser();
    }

    public function testListActionDone()
    {
        //unauthenticated request
        $this->unauthRequest('/tasks/done');

        //authenticated as User
        $this->logInAsUserWithUsername();
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches à faire")')->count());
        $this->deleteTestUser();

        //authenticated as Admin
        $this->logInAsAdminWithUsername();
        $crawler = $this->client->request('GET', '/tasks/done');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Consulter la liste des tâches à faire")')->count());
        $this->deleteTestUser();
    }

    public function testCreateAction()
    {
        //unauthenticated request
        $this->unauthRequest('/tasks/create');

        //authenticated as User
        $this->logInAsUserWithUsername();
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Retour à la liste des tâches")')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form->setValues(['task[title]' => 'testTask', 'task[content]' => 'contenue de la tâche de test']);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        $this->deleteTestTask();
        $this->deleteTestUser();

    }

    public function editAction()
    {
        $this->createTask();
        $testTask = $this->em->getRepository('AppBundle:Task')->findOneByTitle('testTask');
        $TestTaskId = $testTask->getId();

        //unauthenticated request
        $this->unauthRequest('tasks/'. $TestTaskId .'/edit');

        //authenticated as User
        $this->logInAsUser();
        $crawler = $this->client->request('GET', 'tasks/'. $TestTaskId .'/edit');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('html:contains("Content")')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form->setValues(['task[title]' => 'testTask', 'task[content]' => 'contenue de la tâche de test modifié']);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        $this->deleteTestUser();
        $this->deleteTestTask();
    }

    public function testDeleteTaskAction()
    {
        $this->createTask();
        $testTask = $this->em->getRepository('AppBundle:Task')->findOneByTitle('testTask');
        $TestTaskId = $testTask->getId();

        //unauthenticated request
        $this->unauthRequest('tasks/'. $TestTaskId .'/delete');

        //author can delete his task
        $this->logInAsUserWithUsername();
        $this->client->request('GET', 'tasks/'. $TestTaskId .'/delete');
        $crawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());

        //user can't delete when it's not his post
        $this->createTaskWithOtherAuthor();
        $testTask = $this->em->getRepository('AppBundle:Task')->findOneByTitle('testTask');
        $TestTaskId = $testTask->getId();
        $this->client->request('GET', 'tasks/'. $TestTaskId .'/delete');
        $crawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('div.alert.alert-danger')->count());
        $this->deleteTestUser();

        //admin can delete anonyme post
        $this->logInAsAdminWithUsername();
        $testTask = $this->em->getRepository('AppBundle:Task')->findOneByTitle('testTask');
        $TestTaskId = $testTask->getId();
        $this->client->request('GET', 'tasks/'. $TestTaskId .'/delete');
        $crawler = $this->client->followRedirect();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('div.alert.alert-success')->count());
        $this->deleteTestUser();
    }

    public function testToggleTaskAction()
    {

        $this->createTask();
        $testTask = $this->em->getRepository('AppBundle:Task')->findOneByTitle('testTask');
        $TestTaskId = $testTask->getId();

        //unauthenticated request
        $this->unauthRequest('tasks/'. $TestTaskId .'/toggle');

        //test task done
        $this->logInAsUserWithUsername();
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

        $this->deleteTestUser();
        $this->deleteTestTask();
    }
}