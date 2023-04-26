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
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\ClassificationBundle\Model\CategoryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

final class BrowseMediaAction
{
    private Environment $twig;

    /**
     * @var AdminInterface<MediaInterface>
     */
    private AdminInterface $admin;

    private Pool $pool;

    private string $template;

    private ?CsrfTokenManagerInterface $csrfTokenManager;

    private ?CategoryManagerInterface $categoryManager;

    /**
     * @param AdminInterface<MediaInterface> $admin
     */
    public function __construct(
        Environment $twig,
        AdminInterface $admin,
        Pool $pool,
        string $template,
        ?CsrfTokenManagerInterface $csrfTokenManager = null,
        ?CategoryManagerInterface $categoryManager = null
    ) {
        $this->twig             = $twig;
        $this->admin            = $admin;
        $this->pool             = $pool;
        $this->template         = $template;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->categoryManager  = $categoryManager;
    }

    /**
     * @throws AccessDeniedException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __invoke(Request $request): Response
    {
        $this->admin->checkAccess('list');

        $filters = $request->query->all('filter');

        // set the default context
        $context = $this->getContext($filters);

        $rootCategory = null;
        if (null !== $this->categoryManager) {
            $rootCategory = $this->getRootCategoryForContext($context);
        }

        $datagrid = $this->admin->getDatagrid();
        $datagrid->setValue('context', null, $context);
        $datagrid->setValue('providerName', null, $this->admin->getPersistentParameter('provider'));

        if (null !== $rootCategory && [] === $filters) {
            $datagrid->setValue('category', null, $rootCategory->getId());
        }

        if (null !== $this->categoryManager && $request->query->has('category')) {
            $category = $this->categoryManager->findOneBy([
                'id'      => $request->query->get('category'),
                'context' => $context,
            ]);

            if (null !== $category) {
                $datagrid->setValue('category', null, $category->getId());
            } else {
                $datagrid->setValue('category', null, $rootCategory->getId());
            }
        }

        $formats = $this->getFormats($datagrid);

        $formView = $datagrid->getForm()->createView();

        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        return new Response($this->twig->render($this->template, [
            'base_template'  => $this->admin->getTemplateRegistry()->getTemplate('layout'),
            'admin'          => $this->admin,
            'action'         => 'ckeditor_browse',
            'form'           => $formView,
            'datagrid'       => $datagrid,
            'root_category'  => $rootCategory,
            'formats'        => $formats,
            'csrf_token'     => $this->getCsrfToken('sonata.batch'),
            'export_formats' => [],
        ]));
    }

    private function getCsrfToken(string $intention): ?string
    {
        if (null === $this->csrfTokenManager) {
            return null;
        }

        return $this->csrfTokenManager->getToken($intention)->getValue();
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     *
     * @phpstan-param string[] $theme
     */
    private function setFormTheme(FormView $formView, array $theme): void
    {
        $this->twig
            ->getRuntime(FormRenderer::class)
            ->setTheme($formView, $theme)
        ;
    }

    private function getRootCategoryForContext(string $context): ?CategoryInterface
    {
        if (null === $this->categoryManager) {
            return null;
        }

        $rootCategories = $this->categoryManager->getAllRootCategories(false);

        foreach ($rootCategories as $category) {
            if (null === $category->getContext()) {
                continue;
            }

            if ($category->getContext()->getId() === $context) {
                return $category;
            }
        }

        return null;
    }

    /**
     * @param mixed[] $filters
     */
    private function getContext(array $filters): string
    {
        if (!\array_key_exists('context', $filters)) {
            $context = $this->admin->getPersistentParameter('context', $this->pool->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        return (string) $context;
    }

    /**
     * @psalm-param DatagridInterface<ProxyQueryInterface<MediaInterface>> $datagrid
     *
     * @return array<array-key, mixed[]>
     */
    private function getFormats(DatagridInterface $datagrid): array
    {
        $formats = [];
        foreach ($datagrid->getResults() as $media) {
            \assert($media instanceof MediaInterface);

            $formats[$media->getId()] = $this->pool->getFormatNamesByContext($media->getContext());
        }

        return $formats;
    }
}
