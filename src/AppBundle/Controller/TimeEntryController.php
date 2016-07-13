<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TimeEntry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TimeEntryController
 *
 * @package AppBundle\Controller
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class TimeEntryController extends Controller {

    const DEFAULT_ORDER_BY = array('date' => 'ASC', 'startTime' => 'ASC');

    /**
     * @Route("/time", name="time")
     */
    public function indexAction() {
        $runningTimeEntry = $this->getDoctrine()->getRepository('AppBundle:TimeEntry')->findOneBy(array('employee' => $this->getUser()->getEmployee(), 'endTime' => null));

        $parameters = array(
            'timeEntry' => $runningTimeEntry,
            'timerRunning' => !!$runningTimeEntry,
            'timeEntriesGroupedByDate' => $this->getDoctrine()->getRepository('AppBundle:TimeEntry')->getCurrentGroupedByDay()
        );
        return $this->render('::time.html.twig', $parameters);
    }

    /**
     * @Route("/data/time", name="get_time_entries", methods={"GET"})
     */
    public function getTimeEntriesAction(Request $request) {
        if ($request->get('html')) {
            $parameters = array(
                'timeEntriesGroupedByDate' => $this->getDoctrine()->getRepository('AppBundle:TimeEntry')->getCurrentGroupedByDay()
            );
            $renderedTemplate = $this->get('twig')->render(':snippets:recorded_worktime.html.twig', $parameters);
            return new JsonResponse(array('html' => $renderedTemplate));
        }
        return new JsonResponse($this->serializeTimeEntries($this->getAllTimeEntries()));
    }

    /**
     * @param array $timeEntries An array with TimeEntry objects
     *
     * @return array    An array with serialized TimeEntries
     */
    private function serializeTimeEntries(array $timeEntries) {
        $serializedTimeEntries = array();
        foreach ($timeEntries as $timeEntry) {
            $serializedTimeEntries[] = $timeEntry->serialize();
        }
        return $serializedTimeEntries;
    }

    /**
     * Get all TimeEntries for the current employee (based on the user currently logged in)
     *
     * @param array $orderBy
     *
     * @return \AppBundle\Entity\TimeEntry[]|array
     */
    private function getAllTimeEntries($orderBy = self::DEFAULT_ORDER_BY) {
        return $this->getDoctrine()->getRepository('AppBundle:TimeEntry')->findBy(array('employee' => $this->getUser()->getEmployee()), $orderBy);
    }

    /**
     * @Route("/data/time/{id}", name="get_time_entry", methods={"GET"})
     */
    public function getTimeEntryAction(Request $request, TimeEntry $timeEntry) {
        return new JsonResponse($timeEntry->serialize());
    }

    /**
     * @Route("/data/time", name="create_time_entry", methods={"POST"})
     */
    public function createTimeEntryAction(Request $request) {
        $timeEntry = new TimeEntry();
        $timeEntry->setEmployee($this->getUser()->getEmployee());
        $timeEntry->setStartTimeFormated($request->get('startTime'));
        $timeEntry->setEndTimeFormated($request->get('endTime'));
        $timeEntry->setDateFormated($request->get('date'), 'Y-m-d');

        $this->validateTimeEntry($timeEntry);
        $this->getDoctrine()->getManager()->persist($timeEntry);
        $this->getDoctrine()->getManager()->flush();

        $message = array(
            'message' => $this->get('translator')->trans('message.create.successful'),
            'data' => $timeEntry->serialize()
        );
        $location = $this->get('router')->generate('get_time_entry', array('id' => $timeEntry->getId()));

        return new JsonResponse($message, 201, array('Location' => $location));
    }

    /**
     * Validates the TimeEntry object and throws a BadRequestHttpException when the object is not valid
     *
     * @param TimeEntry $timeEntry
     */
    private function validateTimeEntry(TimeEntry $timeEntry) {
        $errors = $this->get('validator')->validate($timeEntry);
        if (count($errors)) {
            throw new BadRequestHttpException((string) $errors);
        }
    }

    /**
     * @Route("/data/time/{id}", name="update_time_entry", methods={"PUT", "POST"})
     */
    public function updateTimeEntryAction(Request $request, TimeEntry $timeEntry) {
        $timeEntry->setStartTimeFormated($request->get('startTime'));
        $timeEntry->setEndTimeFormated($request->get('endTime'));
        $timeEntry->setDateFormated($request->get('date'));

        $this->validateTimeEntry($timeEntry);
        $this->getDoctrine()->getManager()->flush();

        $message = array(
            'message' => $this->get('translator')->trans('message.update.successful'),
            'data' => $timeEntry->serialize()
        );
        return new JsonResponse($message);
    }

    /**
     * @Route("/data/time/{id}", name="delete_time_entry", methods={"DELETE"})
     */
    public function deleteTimeEntryAction(Request $request, TimeEntry $timeEntry) {
        $this->getDoctrine()->getManager()->remove($timeEntry);
        $this->getDoctrine()->getManager()->flush();

        $message = array(
            'message' => $this->get('translator')->trans('message.delete.successful'),
            'data' => $this->serializeTimeEntries($this->getAllTimeEntries())
        );
        return new JsonResponse($message);
    }

}
