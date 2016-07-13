<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * Class TimeEntryRepository
 *
 * @package AppBundle\Repository
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class TimeEntryRepository extends EntityRepository {

    const CURRENT_TIME_PERIOD = '1 month ago';

    public function getCurrentGroupedByDay() {
        $query = $this->getEntityManager()->createQueryBuilder()->select('t')
                      ->from('AppBundle:TimeEntry', 't')
                      ->where('t.date > :date')
                      ->setParameter('date', new \DateTime(self::CURRENT_TIME_PERIOD))
                      ->andWhere('t.endTime IS NOT NULL')
                      ->orderBy('t.date', 'DESC')
                      ->addOrderBy('t.startTime', 'ASC');
        $result = $query->getQuery()->getResult();

        $groupedResult = array();
        foreach ($result as $timeEntry) {
            $currentWeek = 'KW ' . $timeEntry->getDate()->format('W Y');
            if (!isset($groupedResult[$currentWeek])) {
                $groupedResult[$currentWeek]['total'] = 0;
            }
            $groupedResult[$currentWeek]['dates'][$timeEntry->getDate()->format('Y-m-d')][] = $timeEntry;
            $groupedResult[$currentWeek]['total'] += (int) $timeEntry->getEndTime()->format('U') - $timeEntry->getStartTime()->format('U');
        }

        return $groupedResult;
    }

    public function getTimeEntriesGroupedByDayForDates($fromDate, $toDate) {
        $query = $this->getEntityManager()->createQueryBuilder()->select('t')
                      ->from('AppBundle:TimeEntry', 't')
                      ->where('t.date >= :fromDate')
                      ->setParameter('fromDate', new \DateTime($fromDate))
                      ->andWhere('t.date <= :toDate')
                      ->setParameter('toDate', new \DateTime($toDate))
                      ->andWhere('t.endTime IS NOT NULL')
                      ->orderBy('t.date', 'DESC')
                      ->addOrderBy('t.startTime', 'ASC');
        $result = $query->getQuery()->getResult();

        $groupedResult = array('total' => 0);
        foreach ($result as $timeEntry) {
            $groupedResult['dates'][$timeEntry->getDate()->format('Y-m-d')][] = $timeEntry;
            $groupedResult['total'] += (int) $timeEntry->getEndTime()->format('U') - $timeEntry->getStartTime()->format('U');
        }

        return $groupedResult;
    }

}