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

namespace Nucleos\SonataCKEditorBundle\DependencyInjection;

use Nucleos\SonataCKEditorBundle\Action\BrowseMediaAction;
use Nucleos\SonataCKEditorBundle\Action\UploadMediaAction;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SonataCKEditorExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array<mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('action.php');
        $loader->load('admin.php');

        $container->getDefinition(BrowseMediaAction::class)
            ->replaceArgument(3, $config['templates']['browse'])
        ;

        $container->getDefinition(UploadMediaAction::class)
            ->replaceArgument(3, $config['templates']['upload'])
        ;
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('fos_ck_editor')) {
            return;
        }

        $configs      = $container->getExtensionConfig($this->getAlias());
        $resolvingBag = $container->getParameterBag();
        $configs      = $resolvingBag->resolveValue($configs);
        $config       = $this->processConfiguration(new Configuration(), $configs);

        if (!$config['autoconfig']) {
            return;
        }

        $editorConfig = [
            'filebrowserBrowseRoute' => $config['autoconfig']['browseRoute'],

            'filebrowserImageBrowseRoute'           => $config['autoconfig']['browseRoute'],
            'filebrowserImageBrowseRouteParameters' => [
                'provider' => 'sonata.media.provider.image',
            ],

            'filebrowserUploadMethod'          => 'form',
            'filebrowserUploadRoute'           => $config['autoconfig']['uploadRoute'],
            'filebrowserUploadRouteParameters' => [
                'provider' => $config['autoconfig']['fileProvider'],
            ],

            'filebrowserImageUploadRoute'           => $config['autoconfig']['uploadRoute'],
            'filebrowserImageUploadRouteParameters' => [
                'provider' => $config['autoconfig']['imageProvider'],
                'context'  => $config['autoconfig']['imageContext'],
                'format'   => $config['autoconfig']['imageFormat'],
            ],
        ];

        $configMap = [];
        foreach ($config['autoconfig']['contexts'] as $context) {
            $configMap[$context] = $editorConfig;
        }

        $container->prependExtensionConfig('fos_ck_editor', [
            'configs' => $configMap,
        ]);
    }
}
