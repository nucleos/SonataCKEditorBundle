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

namespace Nucleos\SonataCKEditorBundle\Admin;

use Nucleos\SonataCKEditorBundle\Action\BrowseMediaAction;
use Nucleos\SonataCKEditorBundle\Action\UploadMediaAction;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

final class CKEditorAdminExtension extends AbstractAdminExtension
{
    public function configureRoutes(AdminInterface $admin, RouteCollectionInterface $collection): void
    {
        $collection->add('ckeditor_browse', 'ckeditor-browse', [
            '_controller' => BrowseMediaAction::class,
        ]);

        $collection->add('ckeditor_upload', 'ckeditor-upload', [
            '_controller' => UploadMediaAction::class,
        ]);
    }

    public function configurePersistentParameters(AdminInterface $admin, array $parameters): array
    {
        if (!$admin->hasRequest()) {
            return $parameters;
        }

        $request = $admin->getRequest();

        $parameters['CKEditor'] = $request->query->get('CKEditor');
        $parameters['CKEditorFuncNum'] = $request->query->get('CKEditorFuncNum');

        return $parameters;
    }
}
