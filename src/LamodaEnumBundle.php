<?php

namespace Lamoda\EnumBundle;

use Lamoda\EnumBundle\DBAL\EnumTypeInitializer;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class LamodaEnumBundle extends Bundle
{
    public function boot(): void
    {
        // Fetch service from the container in order to register doctrine types
        $this->container->get(EnumTypeInitializer::class);
    }
}
