<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUsername()
    {
        $user = new User;
        $user->setUsername('testUsername');

        $this->assertEquals('testUsername', $user->getUsername());
    }

    public function testEmail()
    {
        $user = new User;
        $user->setEmail('test@gmail.com');

        $this->assertEquals('test@gmail.com', $user->getEmail());
    }

    public function testRoles()
    {
        $user = new User;
        $user->setRoles(['ROLE_USER']);

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testPassword()
    {
        $user = new User;
        $user->setPassword('AZERTY');

        $this->assertEquals('AZERTY', $user->getPassword());
    }

    public function testSalt()
    {
        $user = new User;
        $this->assertEquals(null, $user->getSalt());
    }
}
