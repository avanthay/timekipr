<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Time;

/**
 * TimeEntry
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TimeEntryRepository")
 */
class TimeEntry extends AbstractEntity {

    const DEFAULT_TIME_FORMAT = 'H:i';
    const DEFAULT_DATE_FORMAT = 'd.m.Y';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="time", nullable=false)
     * @NotNull()
     * @Time()
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="time", nullable=true)
     * @Time()
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     * @NotNull()
     * @Date()
     */
    private $date;

    /**
     * @var Employee
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Employee")
     * @NotNull()
     */
    private $employee;

    public function __construct(Employee $employee = null, \DateTime $startTime = null, \DateTime $date = null) {
        $this->employee = $employee;
        $this->startTime = $startTime;
        $this->date = $date;
    }

    /**
     * @return array    The serialized TimeEntry object
     */
    public function serialize() {
        return array(
            'id' => $this->getId(),
            'startTime' => $this->getStartTimeFormatted(),
            'endTime' => $this->getEndTimeFormatted(),
            'date' => $this->getDateFormatted(),
            'employeeId' => $this->getEmployee()->getId()
        );
    }

    /**
     * @param string $format
     *
     * @return null|string
     */
    public function getStartTimeFormatted($format = self::DEFAULT_TIME_FORMAT) {
        if ($this->startTime instanceof \DateTime) {
            return $this->startTime->format($format);
        }
        return null;
    }

    /**
     * @param string $format
     *
     * @return null|string
     */
    public function getEndTimeFormatted($format = self::DEFAULT_TIME_FORMAT) {
        if ($this->endTime instanceof \DateTime) {
            return $this->endTime->format($format);
        }
        return null;
    }

    /**
     * @param string $format
     *
     * @return null|string
     */
    public function getDateFormatted($format = self::DEFAULT_DATE_FORMAT) {
        if ($this->date instanceof \DateTime) {
            return $this->date->format($format);
        }
        return null;
    }

    /**
     * Get employee
     *
     * @return Employee
     */
    public function getEmployee() {
        return $this->employee;
    }

    /**
     * Set employee
     *
     * @param Employee $employee
     *
     * @return TimeEntry
     */
    public function setEmployee(Employee $employee = null) {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime() {
        return $this->startTime;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return TimeEntry
     */
    public function setStartTime($startTime) {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @param        $startTime
     * @param string $format
     *
     * @return bool|\DateTime   The created DateTime Object on success, false otherwise
     */
    public function setStartTimeFormated($startTime, $format = self::DEFAULT_TIME_FORMAT) {
        if ($timeObject = \DateTime::createFromFormat($format, $startTime)) {
            $this->startTime = $timeObject;
            return $timeObject;
        }
        return false;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return TimeEntry
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @param        $endTime
     * @param string $format
     *
     * @return bool|\DateTime   The created DateTime Object on success, false otherwise
     */
    public function setEndTimeFormated($endTime, $format = self::DEFAULT_TIME_FORMAT) {
        if ($timeObject = \DateTime::createFromFormat($format, $endTime)) {
            $this->endTime = $timeObject;
            return $timeObject;
        }
        return false;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TimeEntry
     */
    public function setDate($date) {
        $this->date = $date;

        return $this;
    }

    /**
     * @param        $date
     * @param string $format
     *
     * @return bool|\DateTime   The created DateTime Object on success, false otherwise
     */
    public function setDateFormated($date, $format = self::DEFAULT_DATE_FORMAT) {
        if ($dateObject = \DateTime::createFromFormat($format, $date)) {
            $this->date = $dateObject;
            return $dateObject;
        }
        return false;
    }
}
