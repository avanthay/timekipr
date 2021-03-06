<?php


namespace AppBundle\Tests\Controller;

use AppBundle\Controller\EmployeeController;
use AppBundle\Entity\Employee;
use AppBundle\Entity\User;
use AppBundle\Tests\TestUtils\Utils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


/**
 * Class EmployeeControllerTest
 * @package AppBundle\Tests\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class EmployeeControllerTest extends WebTestCase {

    public function employeeProvider() {
        return array(
            array($this->createEmployee('firstname', 'lastname', 'firstname.lastname@domain.com', array('ROLE_TEAM_LEADER')))
        );
    }

    /**
     * Helper function to create an employee for testing purpose
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param array $roles
     *
     * @return Employee
     */
    private function createEmployee($firstname, $lastname, $email, array $roles) {
        $employee = new Employee();
        $employee->setFirstname($firstname);
        $employee->setLastname($lastname);
        $employee->setUser(new User());
        $employee->getUser()->setEmail($email);
        foreach ($roles as $role) {
            $employee->getUser()->addRole($role);
        }
        return $employee;
    }

    /**
     * @dataProvider employeeProvider
     */
    public function testSerializeEmployee(Employee $employee) {
        $serializedEmployee = Utils::invokeMethod(new EmployeeController(), 'serializeEmployee', array($employee));
        $this->assertInternalType('array', $serializedEmployee);

        $this->assertEquals($employee->getFirstname(), $serializedEmployee['firstname']);
        $this->assertEquals($employee->getUser()->getEmail(), $serializedEmployee['email']);
        $this->assertEquals($employee->getUser()->hasRole('ROLE_TEAM_LEADER'), $serializedEmployee['isTeamLeader']);
        $this->assertEquals($employee->getUser()->hasRole('ROLE_MANAGER'), $serializedEmployee['isManager']);
    }

}