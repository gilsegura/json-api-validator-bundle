<?php

declare(strict_types=1);

namespace JSONAPIValidatorBundle;

use JSONAPIValidatorBundle\DependencyInjection\JSONAPIValidatorExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class JSONAPIValidatorBundle extends AbstractBundle
{
    #[\Override]
    public function getContainerExtension(): ExtensionInterface
    {
        return new JSONAPIValidatorExtension();
    }
}
