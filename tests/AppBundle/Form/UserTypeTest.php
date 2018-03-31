<?php

namespace AppBundle\Tests\Form\User;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testSubmitValidDataUser()
    {
        $formData = array(
            'roles' => ['ROLE_ADMIN'],
            'username' => 'testUser',
            'password' => array('first' => 'test', 'second' => 'test'),
            'email' => 'test@gmail.com'
        );

        $form = $this->factory->create(UserType::class);

        $user = new User();
        $user->setRoles($formData['roles']);
        $user->setUsername($formData['username']);
        $user->setPassword($formData['password']);
        $user->setEmail($formData['email']);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($user->getRoles(), $form->get('roles')->getData());
        $this->assertEquals($user->getUsername(), $form->get('username')->getData());
        $this->assertEquals($user->getPassword(), $form->get('password')->getData());
        $this->assertEquals($user->getEmail(), $form->get('email')->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}