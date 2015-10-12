<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

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
     * @var Employee
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Employee", mappedBy="user")
     */
    private $employee;

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
