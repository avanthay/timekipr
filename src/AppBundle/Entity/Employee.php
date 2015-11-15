<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Employee
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Employee extends AbstractEntity {

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="employee", cascade={"persist", "remove"})
     * @Valid()
     * @NotNull()
     */
    private $user;

    public function __construct($firstname = null, $lastname = null) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }


    /**
     * @return string The last and first name of the employee separated by a comma.
     */
    public function __toString() {
        if ($this->lastname || $this->firstname) {
            return implode(', ', array_filter(array($this->firstname, $this->lastname)));
        }
        return implode('-', array_filter(array('Employee', $this->id)));
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Employee
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Employee
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser() {
        if (!$this->user) {
            $this->user = new User();
        }
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Employee
     */
    public function setUser(\AppBundle\Entity\User $user = null) {
        $this->user = $user;

        return $this;
    }
}
