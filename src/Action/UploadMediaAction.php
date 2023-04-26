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

namespace Nucleos\SonataCKEditorBundle\Action;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

final class UploadMediaAction
{
    private Environment $twig;

    /**
     * @var AdminInterface<MediaInterface>
     */
    private AdminInterface $admin;

    private Pool $pool;

    private string $template;

    private MediaManagerInterface $mediaManager;

    private ?CategoryManagerInterface $categoryManager;

    /**
     * @param AdminInterface<MediaInterface> $admin
     */
    public function __construct(
        Environment $twig,
        AdminInterface $admin,
        Pool $pool,
        string $template,
        MediaManagerInterface $mediaManager,
        ?CategoryManagerInterface $categoryManager = null,
    ) {
        $this->twig            = $twig;
        $this->admin           = $admin;
        $this->pool            = $pool;
        $this->template        = $template;
        $this->mediaManager    = $mediaManager;
        $this->categoryManager = $categoryManager;
    }

    /**
     * @throws AccessDeniedException
     */
    public function __invoke(Request $request): Response
    {
        $this->admin->checkAccess('create');

        if (!$request->isMethod('POST')) {
            throw new MethodNotAllowedHttpException(['POST']);
        }

        $provider = $request->query->get('provider', '');

        $file = $request->files->get('upload');

        if ('' === $provider || null === $file) {
            throw new BadRequestHttpException();
        }

        $context = $request->query->get('context', 'default');

        $media = $this->mediaManager->create();
        $media->setBinaryContent($file);
        $media->setProviderName($provider);
        $media->setContext($context);
        $media->setCategory($this->getCategory($context));

        $this->mediaManager->save($media);

        $this->admin->createObjectSecurity($media);

        $format = $this->pool->getProvider($provider)->getFormatName(
            $media,
            $request->query->get('format', MediaProviderInterface::FORMAT_REFERENCE)
        );

        return new Response($this->twig->render($this->template, [
            'action' => 'ckeditor-browse',
            'object' => $media,
            'format' => $format,
        ]));
    }

    private function getCategory(?string $context): ?CategoryInterface
    {
        $categoryId = $this->admin->getPersistentParameter('category');

        if (null === $this->categoryManager || null === $categoryId) {
            return null;
        }

        $category = $this->categoryManager->find($categoryId);

        if (null === $category) {
            return null;
        }

        $categoryContext = $category->getContext();

        if (null === $categoryContext || $categoryContext->getId() !== $context) {
            return null;
        }

        return $category;
    }
}
