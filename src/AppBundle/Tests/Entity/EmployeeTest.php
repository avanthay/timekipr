<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Employee;
use AppBundle\Entity\User;
use AppBundle\Tests\TestUtils\Utils;


/**
 * Class EmployeeTest
 * @package AppBundle\Tests\Entity
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class EmployeeTest extends \PHPUnit_Framework_TestCase {

    public function employeeProvider() {
        $employeeWithoutNameWithId = new Employee();
        Utils::setProperty($employeeWithoutNameWithId, 'id', '23');

        return array(
            array(new Employee('firstname', 'lastname'), 'firstname, lastname'),
            array(new Employee(null, 'lastname'), 'lastname'),
            array(new Employee('firstname', null), 'firstname'),
            array($employeeWithoutNameWithId, 'Employee-23'),
            array(new Employee(), 'Employee')
        );
    }

    /**
     * @dataProvider employeeProvider
     */
    public function testToString(Employee $employee, $expectedStringRepresentation) {
        $this->assertEquals($expectedStringRepresentation, (string) $employee);
    }

    /**
     * Tests if a new User instance is created when no User is set
     */
    public function testGetUser() {
        $employeeWithoutUser = new Employee();
        $this->assertInstanceOf(User::class, $employeeWithoutUser->getUser());
        $this->assertNull($employeeWithoutUser->getUser()->getId());
    }


}