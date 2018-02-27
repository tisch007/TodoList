<?php

namespace Tests\AppBundle;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;

trait TestTrait{

    private $client = null;

    private $em = null;

    private $container;

    private function logInAsAdmin()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';
        $token = new UsernamePasswordToken('admin', null, $firewallContext, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function logInAsUser()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';
        $token = new UsernamePasswordToken('user', null, $firewallContext, array('ROLE_USER'));
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function deleteTestUser()
    {
        $testUser = $this->em->getRepository('AppBundle:User')->findOneBy(['username' => 'testUser']);
        $this->em->remove($testUser);
        $this->em->flush();
    }

    private function deleteTestTask()
    {
        $testTask = $this->em->getRepository('AppBundle:Task')->findOneBy(['title' => 'testTask']);
        $this->em->remove($testTask);
        $this->em->flush();
    }
    private function logInAsUserWithUsername()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';

        $testUser = new User();
        $testUser->setUsername('testUser');
        $testUser->setPassword('a');
        $testUser->setRoles(array('ROLE_USER'));
        $testUser->setEmail('test@gmail.com');
        $this->em->persist($testUser);
        $this->em->flush();

        $token = new UsernamePasswordToken($testUser, null, $firewallContext, $testUser->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function logInAsAdminWithUsername()
    {
        $session = $this->client->getContainer()->get('session');
        $firewallContext = 'main';

        $testUser = new User();
        $testUser->setUsername('testUser');
        $testUser->setPassword('a');
        $testUser->setRoles(array('ROLE_ADMIN'));
        $testUser->setEmail('test@gmail.com');
        $this->em->persist($testUser);
        $this->em->flush();

        $token = new UsernamePasswordToken($testUser, null, $firewallContext, $testUser->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function createTask()
    {
        $testUser = 'testUser';
        $taskTest = new Task();
        $taskTest->setTitle('testTask');
        $taskTest->setContent('tâche de test');
        $taskTest->setAuthor($testUser);
        $this->em->persist($taskTest);
        $this->em->flush();
    }

    private function createTaskWithOtherAuthor()
    {
        $testUser = 'anonyme';
        $taskTest = new Task();
        $taskTest->setTitle('testTask');
        $taskTest->setContent('tâche de test');
        $taskTest->setAuthor($testUser);
        $this->em->persist($taskTest);
        $this->em->flush();
    }

    private function unauthRequest(string $url)
    {
        $this->client->request('GET', $url);
        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertSame(1, $crawler->filter('form.loginForm')->count());
    }
}