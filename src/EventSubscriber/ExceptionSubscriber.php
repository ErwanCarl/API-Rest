<?php

namespace App\EventSubscriber;

use Exception;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }   
    
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if(($this->requestStack->getCurrentRequest()->get('customer_id')) !== null) {
            $customerId = ctype_digit($this->requestStack->getCurrentRequest()->get('customer_id'));
        }

        if(($this->requestStack->getCurrentRequest()->get('id')) !== null) {
            $phoneId = ctype_digit($this->requestStack->getCurrentRequest()->get('id'));
        }
    
        if (isset($customerId) && $exception instanceof NotFoundHttpException && $customerId !== true && !($exception->getPrevious() instanceof ResourceNotFoundException)) {
            $data = [
                'status' => 400,
                'message' => "Le paramètre 'customer_id' n'accepte que les chiffres."
            ];
            $event->setResponse(new JsonResponse($data, 400));

        } elseif (isset($phoneId) && $exception instanceof NotFoundHttpException && $phoneId !== true && !($exception->getPrevious() instanceof ResourceNotFoundException)) {
            $data = [
                'status' => 400,
                'message' => "Le paramètre 'id' n'accepte que les chiffres."
            ];
            $event->setResponse(new JsonResponse($data, 400));
         
        } elseif ($exception instanceof NotFoundHttpException && !($exception->getPrevious() instanceof ResourceNotFoundException)) {
            $data = [
                'status' => 404,
                'message' => 'L\'objet recherché n\'a pas été trouvé, l\'identifiant n\'existe pas, veuillez vérifier les id de votre url.'
            ];
            $event->setResponse(new JsonResponse($data));

        } elseif ($exception instanceof NotFoundHttpException && $exception->getPrevious() instanceof ResourceNotFoundException) {
            $data = [
                'status' => 404,
                'message' => 'L\'url entrée est erronée, la route n\'existe pas.'
            ];
            $event->setResponse(new JsonResponse($data));

        } elseif ($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));

        } elseif ($exception instanceof Exception && ($exception->getCode() === 666 || $exception->getCode() === 999)) {
            $data = [
                'status' => 404,
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data, 404));
        
        } else {
            $data = [
                'status' => 500, 
                'message' => $exception->getMessage()
            ];

            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
