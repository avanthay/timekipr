<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ReportControllerController extends Controller {

    /**
     * @Route("/report", name="report", methods={"GET", "POST"} )
     */
    public function indexAction(Request $request) {
        $fromDate = $request->get('fromDate') ?: '1 month ago';
        $toDate = $request->get('toDate') ?: 'now';
        $parameters = array(
            'timeEntriesGroupedByDate' => $this->getDoctrine()->getRepository('AppBundle:TimeEntry')->getTimeEntriesGroupedByDayForDates($fromDate, $toDate),
            'fromDate' => new \DateTime($fromDate),
            'toDate' => new \DateTime($toDate)
        );
        return $this->render('::report.html.twig', $parameters);
    }

}
