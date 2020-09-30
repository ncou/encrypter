<?php

declare(strict_types=1);

namespace Chiron\Encrypter\Bootloader;

use Chiron\Core\Directories;
use Chiron\Bootload\AbstractBootloader;
use Chiron\PublishableCollection;

final class PublishEncrypterBootloader extends AbstractBootloader
{
    public function boot(PublishableCollection $publishable, Directories $directories): void
    {
        // copy the configuration file template from the package "config" folder to the user "config" folder.
        $publishable->add(__DIR__ . '/../../config/encrypter.php.dist', $directories->get('@config/encrypter.php'));
    }
}
