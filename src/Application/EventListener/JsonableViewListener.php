<?php

namespace Application\EventListener;

use Dto\Outbound\Jsonable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class JsonableViewListener
{
    public function onKernelView(ViewEvent $event)
    {
        $response = $event->getControllerResult();
        if ($response instanceof Jsonable) {
            $event->setResponse(new JsonResponse($response, $response->getStatusCode()));
        }
    }
}
