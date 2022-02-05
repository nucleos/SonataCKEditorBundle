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

namespace Nucleos\SonataCKEditorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sonata_ck_editor');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode->append($this->addTemplateNode());
        $rootNode->append($this->addAutoConfigNode());

        return $treeBuilder;
    }

    private function addTemplateNode(): NodeDefinition
    {
        $node = (new TreeBuilder('templates'))->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                    ->scalarNode('browse')->defaultValue('@SonataCKEditor/browse.html.twig')->cannotBeEmpty()->end()
                    ->scalarNode('upload')->defaultValue('@SonataCKEditor/upload.html.twig')->cannotBeEmpty()->end()
            ->end();

        return $node;
    }

    private function addAutoConfigNode(): NodeDefinition
    {
        $node = (new TreeBuilder('autoconfig'))->getRootNode();

        $node
            ->canBeUnset()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('contexts')
                    ->prototype('scalar')->end()
                    ->defaultValue(['default'])
                ->end()
                ->scalarNode('browseRoute')->defaultValue('admin_app_media_media_ckeditor_browse')->cannotBeEmpty()->end()
                ->scalarNode('uploadRoute')->defaultValue('admin_app_media_media_ckeditor_upload')->cannotBeEmpty()->end()
                ->scalarNode('fileProvider')->defaultValue('sonata.media.provider.file')->cannotBeEmpty()->end()
                ->scalarNode('imageProvider')->defaultValue('sonata.media.provider.image')->cannotBeEmpty()->end()
                ->scalarNode('imageContext')->defaultValue('default')->cannotBeEmpty()->end()
                ->scalarNode('imageFormat')->defaultNull()->end()
            ->end();

        return $node;
    }
}
