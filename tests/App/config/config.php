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

use Nucleos\SonataCKEditorBundle\Tests\App\Entity\Gallery;
use Nucleos\SonataCKEditorBundle\Tests\App\Entity\GalleryItem;
use Nucleos\SonataCKEditorBundle\Tests\App\Entity\Media;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', ['secret' => 'MySecret']);

    $containerConfigurator->extension('framework', ['assets' => null]);

    $containerConfigurator->extension('framework', ['test' => true]);

    $containerConfigurator->extension('framework', ['translator' => null]);

    $containerConfigurator->extension('framework', ['session' => ['storage_factory_id' => 'session.storage.factory.mock_file']]);

    $containerConfigurator->extension('twig', ['strict_variables' => true]);

    $containerConfigurator->extension('twig', ['exception_controller' => null]);

    $containerConfigurator->extension('twig', ['form_themes' => ['@FOSCKEditor/Form/ckeditor_widget.html.twig']]);

    $containerConfigurator->extension('security', [
        'enable_authenticator_manager' => true,
        'firewalls'                    => ['main' => ['security' => true]],
    ]);

    $containerConfigurator->extension('security', ['access_control' => [['path' => '^/.*', 'role' => 'PUBLIC_ACCESS']]]);

    $containerConfigurator->extension('doctrine', ['dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/data.db', 'logging' => false]]);

    $containerConfigurator->extension('doctrine', ['orm' => ['auto_mapping' => true, 'mappings' => ['App' => ['is_bundle' => false, 'type' => 'attribute', 'dir' => '%kernel.project_dir%/Entity', 'prefix' => 'Nucleos\SonataCKEditorBundle\Tests\App\Entity', 'alias' => 'App']]]]);

    $containerConfigurator->extension('sonata_media', ['db_driver' => 'doctrine_orm']);

    $containerConfigurator->extension('sonata_media', ['force_disable_category' => true]);

    $containerConfigurator->extension('sonata_media', ['default_context' => 'default']);

    $containerConfigurator->extension('sonata_media', ['contexts' => [
        'default'            => ['providers' => ['sonata.media.provider.file']],
        'project'            => ['providers' => ['sonata.media.provider.file']],
        'project_screenshot' => ['providers' => ['sonata.media.provider.file']],
        'project_download'   => ['providers' => ['sonata.media.provider.file']],
    ],
    ]);

    $containerConfigurator->extension('sonata_media', ['cdn' => ['server' => ['path' => '%kernel.project_dir%/upload/media']]]);

    $containerConfigurator->extension('sonata_media', ['filesystem' => ['local' => ['create' => true, 'directory' => '%kernel.cache_dir%/upload']]]);

    $containerConfigurator->extension('sonata_media', ['class' => ['media' => Media::class, 'gallery' => Gallery::class, 'gallery_item' => GalleryItem::class]]);

    $containerConfigurator->extension('fos_ck_editor', ['default_config' => 'custom']);

    $containerConfigurator->extension('fos_ck_editor', ['configs' => [
        'custom' => [
            'locale'         => 'de',
            'height'         => 500,
            'allowedContent' => true,
        ],
    ]]);
};
