<?php

declare(strict_types=1);

/*
 * This file is part of the SonataAutoConfigureBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Nucleos\SonataCKEditorBundle\Action\BrowseMediaAction;
use Nucleos\SonataCKEditorBundle\Action\UploadMediaAction;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(BrowseMediaAction::class)
        ->public()
        ->args([
            service('twig'),
            service('sonata.media.admin.media'),
            service('sonata.media.pool'),
            '',
            service('security.csrf.token_manager')->nullOnInvalid(),
            service('sonata.media.manager.category')->nullOnInvalid(),
        ])
    ;

    $services->set(UploadMediaAction::class)
        ->public()
        ->args([
            service('twig'),
            service('sonata.media.admin.media'),
            service('sonata.media.pool'),
            '',
            service('sonata.media.manager.media'),
            service('sonata.media.manager.category')->nullOnInvalid(),
        ])
    ;
};
