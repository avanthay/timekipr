<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


/**
 * Class ExceptionListener
 *
 * @author  Dave Avanthay <dave@avanthay.ch>
 * @version 1.0
 */
class ExceptionListener {

    public function onKernelException(GetResponseForExceptionEvent $event) {
        if (!$event->getRequest()->isXmlHttpRequest()) {
            return;
        }
        $exception = $event->getException();

        $response = new JsonResponse(array(
            'hasError' => true,
            'error' => $exception->getMessage(),
        ));

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }

}