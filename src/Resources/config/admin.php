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

use Nucleos\SonataCKEditorBundle\Admin\CKEditorAdminExtension;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(CKEditorAdminExtension::class)
        ->public()
        ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media'])
    ;
};
