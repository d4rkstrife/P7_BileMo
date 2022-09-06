<?php

namespace App\EventSubscriber;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class NotFoundRouteExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        //dd($exception);
        if($exception instanceof HttpExceptionInterface){
            $response = new JsonResponse(["error"=>"Message d'erreur"], 404);
            $event->setResponse($response);
        }
    }
    
}