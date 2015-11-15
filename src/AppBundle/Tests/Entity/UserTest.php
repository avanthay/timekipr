<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\User;


/**
 * Class UserTest
 * @package AppBundle\Tests\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class UserTest extends \PHPUnit_Framework_TestCase {

    public function userProvider() {
        $userWithSomeRoles = new User();
        $userWithSomeRoles->setRoles(array('ROLE_MANAGER', 'ROLE_TEAM_LEADER', 'ROLE_ADMIN'));
        return array(
            array(new User(), 'ROLE_SUPER_ADMIN', true),
            array(new User(), 'ROLE_SUPER_ADMIN', false),
            array(clone $userWithSomeRoles, 'ROLE_SUPER_ADMIN', true),
            array(clone $userWithSomeRoles, 'ROLE_SUPER_ADMIN', false),
            array(clone $userWithSomeRoles, 'ROLE_ADMIN', true),
            array(clone $userWithSomeRoles, 'ROLE_ADMIN', false)
        );
    }

    /**
     * @dataProvider userProvider
     */
    public function testUpdateRole(User $user, $role, $active) {
        $user->updateRole($role, $active);
        $this->assertEquals($active, $user->hasRole($role));
    }

    /**
     * Tests if the username property is set, if its not set while setting the email
     */
    public function testSetEmail() {
        $user = new User();
        $this->assertNull($user->getEmail());

        $user->setEmail('foo@bar.com');
        $this->assertEquals($user->getEmail(), $user->getUsername());

        $secondUser = new User();
        $secondUser->setUsername('username');
        $secondUser->setEmail('foo@bar.com');
        $this->assertNotEquals($secondUser->getEmail(), $secondUser->getUsername());
    }

}