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

namespace Nucleos\SonataCKEditorBundle\Tests;

use Nucleos\SonataCKEditorBundle\DependencyInjection\SonataCKEditorExtension;
use Nucleos\SonataCKEditorBundle\SonataCKEditorBundle;
use PHPUnit\Framework\TestCase;

final class SonataCKEditorBundleTest extends TestCase
{
    private SonataCKEditorBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new SonataCKEditorBundle();
    }

    public function testCompilerPasses(): void
    {
        static::assertInstanceOf(SonataCKEditorExtension::class, $this->bundle->getContainerExtension());
    }
}
