<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Employee;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Class EmployeeController
 * @package AppBundle\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class EmployeeController extends Controller {

    /**
     * @Route("/admin/employee", name="employee")
     */
    public function indexAction() {
        return $this->render(':admin:employee_overview.html.twig', array('employees' => $this->serializeEmployees()));
    }

    private function serializeEmployees(array $orderBy = array('lastname' => 'ASC', 'firstname' => 'ASC')) {
        $employees = $this->getDoctrine()->getRepository('AppBundle:Employee')->findBy(array(), $orderBy);
        $serializedEmployees = array();
        foreach ($employees as $employee) {
            $serializedEmployees[] = $this->serializeEmployee($employee);
        }
        return $serializedEmployees;
    }

    private function serializeEmployee(Employee $employee) {
        return $this->resolveSerializedEmployee($employee);
    }

    private function resolveSerializedEmployee(Employee $employee, array $serializedEmployee = array()) {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'id' => $employee->getId(),
            'firstname' => $employee->getFirstname(),
            'lastname' => $employee->getLastname(),
            'email' => $employee->getUser()->getEmail(),
            'isTeamLeader' => $employee->getUser()->hasRole('ROLE_TEAM_LEADER'),
            'isManager' => $employee->getUser()->hasRole('ROLE_MANAGER')
        ));

        try {
            return $resolver->resolve($serializedEmployee);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * todo refactor: one function for each method
     * @Route("/data/employee", name="data_employees")
     */
    public function employeesAction(Request $request) {
        if ($request->isMethod('GET')) {
            return new JsonResponse($this->serializeEmployees());
        }
        if ($request->isMethod('POST')) {
            $employee = $this->createEmployee($request->request->all());
            return new JsonResponse(array(
                'message' => $this->get('translator')->trans('message.create.successful'),
                'data' => $this->serializeEmployee($employee)
            ), 201, array('Location' => $this->get('router')->generate('data_employee', array('id' => $employee->getId()))));
        }
        throw new MethodNotAllowedHttpException(array('GET', 'POST'));
    }

    private function createEmployee(array $serializedEmployee) {
        $userManager = $this->get('fos_user.user_manager');
        $employee = new Employee();
        $employee->setUser($userManager->createUser());
        $employee->getUser()->setPlainPassword($this->get('fos_user.util.token_generator')->generateToken());

        $this->updateEmployeeObject($employee, $serializedEmployee);
        $userManager->updateUser($employee->getUser(), false);
        $this->validateEmployee($employee);

        $this->getDoctrine()->getManager()->persist($employee);
        $this->getDoctrine()->getManager()->flush();
        return $employee;
    }

    private function updateEmployeeObject(Employee $employee, array $serializedEmployee) {
        $serializedEmployee = $this->resolveSerializedEmployee($employee, $serializedEmployee);
        $employee->setFirstname($serializedEmployee['firstname']);
        $employee->setLastname($serializedEmployee['lastname']);

        $user = $employee->getUser();
        $user->setUsername($serializedEmployee['email']);
        $user->setEmail($serializedEmployee['email']);
        $user->updateRole('ROLE_TEAM_LEADER', $serializedEmployee['isTeamLeader']);
        $user->updateRole('ROLE_MANAGER', $serializedEmployee['isManager']);
    }

    private function validateEmployee(Employee $employee) {
        $errors = $this->get('validator')->validate($employee);
        if (count($errors)) {
            throw new BadRequestHttpException((string) $errors);
        }
    }

    /**
     * todo refactor: one function for each method
     * @Route("/data/employee/{id}", name="data_employee")
     */
    public function employeeAction(Request $request, Employee $employee) {
        if ($request->isMethod('GET')) {
            return new JsonResponse($this->serializeEmployee($employee));
        }
        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            $this->updateEmployee($employee, $request->request->all());
            return new JsonResponse(array(
                'message' => $this->get('translator')->trans('message.update.successful'),
                'data' => $this->serializeEmployee($employee)
            ));
        }
        if ($request->isMethod('DELETE')) {
            $this->deleteEmployee($employee);
            return new JsonResponse(array(
                'message' => $this->get('translator')->trans('message.delete.successful')
            ));
        }
        throw new MethodNotAllowedHttpException(array('GET', 'POST', 'PUT', 'DELETE'));
    }

    private function updateEmployee(Employee $employee, array $serializedEmployee) {
        $this->updateEmployeeObject($employee, $serializedEmployee);
        $this->validateEmployee($employee);

        $this->getDoctrine()->getManager()->flush();
    }

    private function deleteEmployee(Employee $employee) {
        $this->getDoctrine()->getManager()->remove($employee);
        $this->getDoctrine()->getManager()->flush();
    }

}