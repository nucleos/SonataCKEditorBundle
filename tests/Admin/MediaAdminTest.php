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

namespace Nucleos\SonataCKEditorBundle\Tests\Admin;

use Nucleos\SonataCKEditorBundle\Tests\IntegrationTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class MediaAdminTest extends IntegrationTestCase
{
    public function testBrowse(): void
    {
        $client = self::createClient();
        $client->request('GET', '/admin/tests/app/media/ckeditor-browse');

        self::assertResponseIsSuccessful();
    }

    public function testUpload(): void
    {
        $uploadedFile = $this->createUploadFile();

        $client = self::createClient();
        $client->request('POST', '/admin/tests/app/media/ckeditor-upload?provider=sonata.media.provider.file&CKEditorFuncNum=callBack', [
        ], [
            'upload' => $uploadedFile,
        ]);

        $response = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertIsString($response);
        self::assertStringContainsString('.txt', $response);
        self::assertStringContainsString('callBack', $response);
    }

    private function createUploadFile(): UploadedFile
    {
        $tmpfile = tempnam(sys_get_temp_dir(), 'symfony');

        \assert(\is_string($tmpfile));

        file_put_contents($tmpfile, 'BINARY CONTENT');

        register_shutdown_function(static function () use ($tmpfile) {
            @unlink($tmpfile);
        });

        return new UploadedFile($tmpfile, 'test-file.txt');
    }
}
