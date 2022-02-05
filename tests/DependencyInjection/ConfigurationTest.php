<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\SonataCKEditorBundle\Tests\DependencyInjection;

use Nucleos\SonataCKEditorBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testOptions(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), []);

        $expected = [
            'templates' => [
                'browse' => '@SonataCKEditor/browse.html.twig',
                'upload' => '@SonataCKEditor/upload.html.twig',
            ],
            'autoconfig' => [
                'contexts' => [
                    0 => 'default',
                ],
                'browseRoute' => 'admin_app_media_media_ckeditor_browse',
                'uploadRoute' => 'admin_app_media_media_ckeditor_upload',
                'fileProvider' => 'sonata.media.provider.file',
                'imageProvider' => 'sonata.media.provider.image',
                'imageContext' => 'default',
                'imageFormat' => null,
            ],
        ];

        static::assertSame($expected, $config);
    }

    public function testOptionsWithDisabledAutoConfig(): void
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), [[
            'autoconfig' => false,
        ]]);

        $expected = [
            'templates' => [
                'browse' => '@SonataCKEditor/browse.html.twig',
                'upload' => '@SonataCKEditor/upload.html.twig',
            ],
        ];

        static::assertSame($expected, $config);
    }
}
