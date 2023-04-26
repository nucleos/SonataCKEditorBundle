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

namespace Nucleos\SonataCKEditorBundle\Tests\DependencyInjection;

use FOS\CKEditorBundle\DependencyInjection\FOSCKEditorExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Nucleos\SonataCKEditorBundle\Action\BrowseMediaAction;
use Nucleos\SonataCKEditorBundle\Action\UploadMediaAction;
use Nucleos\SonataCKEditorBundle\Admin\CKEditorAdminExtension;
use Nucleos\SonataCKEditorBundle\DependencyInjection\SonataCKEditorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class SonataCKEditorExtensionTest extends AbstractExtensionTestCase
{
    public function testParametersInContainer(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(BrowseMediaAction::class);
        $this->assertContainerBuilderHasService(UploadMediaAction::class);
        $this->assertContainerBuilderHasService(CKEditorAdminExtension::class);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(BrowseMediaAction::class, 3, '@SonataCKEditor/browse.html.twig');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(UploadMediaAction::class, 3, '@SonataCKEditor/upload.html.twig');
    }

    public function testLoadWithTwigExtension(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new FOSCKEditorExtension());

        foreach ($this->getContainerExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($container);
            }
        }

        $expected = [
            'configs' => [
                'default' => [
                    'filebrowserBrowseRoute'                => 'admin_app_media_media_ckeditor_browse',
                    'filebrowserImageBrowseRoute'           => 'admin_app_media_media_ckeditor_browse',
                    'filebrowserImageBrowseRouteParameters' => [
                        'provider' => 'sonata.media.provider.image',
                    ],
                    'filebrowserUploadMethod'          => 'form',
                    'filebrowserUploadRoute'           => 'admin_app_media_media_ckeditor_upload',
                    'filebrowserUploadRouteParameters' => [
                        'provider' => 'sonata.media.provider.file',
                    ],
                    'filebrowserImageUploadRoute'           => 'admin_app_media_media_ckeditor_upload',
                    'filebrowserImageUploadRouteParameters' => [
                        'provider' => 'sonata.media.provider.image',
                        'context'  => 'default',
                        'format'   => null,
                    ],
                ],
            ],
        ];

        static::assertSame($expected, $container->getExtensionConfig('fos_ck_editor')[0]);
    }

    protected function getContainerExtensions(): array
    {
        return [new SonataCKEditorExtension()];
    }
}
