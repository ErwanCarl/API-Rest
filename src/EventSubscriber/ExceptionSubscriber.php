<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException && !($exception->getPrevious() instanceof ResourceNotFoundException)) {
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

        } elseif ($exception instanceof AuthenticationException) {
            $data = [
                'status' => 401,
                'message' => 'L\'authentification a échouée, veuillez récupérer votre token et l\'utiliser pour vous authentifier.'
            ];
            $event->setResponse(new JsonResponse($data));

        // } elseif ($exception instanceof AccessDeniedHttpException) {
        //     $data = [
        //         'status' => 403,
        //         'message' => 'Vous n\'avez pas les droits d\'accès à ce client, il n\'est pas un de vos clients, veuillez vérifier l\'url entrée ainsi que l\'identifiant demandé.'
        //     ];
        //     $event->setResponse(new JsonResponse($data));

        } elseif ($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data));

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
