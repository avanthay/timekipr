<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends BaseUser {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Email()
     * @NotBlank()
     */
    protected $email;

    /**
     * @var Employee
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Employee", mappedBy="user")
     */
    private $employee;

    /**
     * Updates the role of a user object.
     *
     * @param string $role The role name
     * @param bool $active Either if the role should be added (true) or removed (false)
     */
    public function updateRole($role, $active) {
        if ($this->hasRole($role) && !$active) {
            $this->removeRole($role);
        } elseif (!$this->hasRole($role) && $active) {
            $this->addRole($role);
        }
    }

    /**
     * Set email and, if there is no username set, set the email as username
     * @param string $email
     * @return $this|\FOS\UserBundle\Model\UserInterface
     */
    public function setEmail($email) {
        if (!$this->username) {
            $this->username = $email;
        }
        return parent::setEmail($email);
    }

    /**
     * Get employee
     *
     * @return \AppBundle\Entity\Employee
     */
    public function getEmployee() {
        return $this->employee;
    }

    /**
     * Set employee
     *
     * @param \AppBundle\Entity\Employee $employee
     *
     * @return User
     */
    public function setEmployee(\AppBundle\Entity\Employee $employee = null) {
        if ($employee instanceof Employee) {
            $employee->setUser($this);
        }
        $this->employee = $employee;

        return $this;
    }
}
