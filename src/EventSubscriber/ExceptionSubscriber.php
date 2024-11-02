<?php

declare(strict_types=1);

namespace JSONAPIValidatorBundle\EventSubscriber;

use JSONAPI\Error\Error;
use JSONAPI\ErrorDocument;
use JSONAPI\Exception\JSONAPIExceptionInterface;
use JSONAPI\Link\Link;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ExceptionSubscriber implements EventSubscriberInterface
{
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (!in_array('application/vnd.api+json', $request->getAcceptableContentTypes())) {
            return;
        }

        $exception = $event->getThrowable();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.api+json');

        if ($exception instanceof JSONAPIExceptionInterface) {
            $document = ErrorDocument::document(...$exception->errors())
                ->withMeta(...$exception->meta())
                ->withLinks(Link::self($event->getRequest()->getRequestUri()));
            $response->setStatusCode($exception->getCode());
        } else {
            $document = ErrorDocument::document(Error::error(
                (string) $exception->getCode(),
                preg_replace('/\\\\/', '_', mb_strtolower($exception::class)),
                preg_replace('/\\\\/', '_', mb_ucfirst($exception::class)),
                $exception->getMessage()
            ))
                ->withLinks(Link::self($event->getRequest()->getRequestUri()));
            $response->setStatusCode(500);
        }

        $response->setContent(json_encode($document->serialize()));

        $event->setResponse($response);
    }
}
