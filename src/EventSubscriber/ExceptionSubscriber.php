<?php

declare(strict_types=1);

namespace JSONAPIValidatorBundle\EventSubscriber;

use JsonApi\ErrorDocument;
use JSONAPIValidator\Exception\JSONAPIValidationExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        /** @var JSONAPIValidationExceptionInterface $exception */
        $exception = $event->getThrowable();

        $document = ErrorDocument::document(...$exception->errors())
            ->withMeta(...$exception->meta());

        $event->setResponse(new JsonResponse($document->serialize()));
    }
}
