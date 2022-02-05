SonataCKEditorBundle
=========================

[![Latest Stable Version](https://poser.pugx.org/nucleos/sonata-ckeditor-bundle/v/stable)](https://packagist.org/packages/nucleos/sonata-ckeditor-bundle)
[![Latest Unstable Version](https://poser.pugx.org/nucleos/sonata-ckeditor-bundle/v/unstable)](https://packagist.org/packages/nucleos/sonata-ckeditor-bundle)
[![License](https://poser.pugx.org/nucleos/sonata-ckeditor-bundle/license)](LICENSE.md)

[![Total Downloads](https://poser.pugx.org/nucleos/sonata-ckeditor-bundle/downloads)](https://packagist.org/packages/nucleos/sonata-ckeditor-bundle)
[![Monthly Downloads](https://poser.pugx.org/nucleos/sonata-ckeditor-bundle/d/monthly)](https://packagist.org/packages/nucleos/sonata-ckeditor-bundle)
[![Daily Downloads](https://poser.pugx.org/nucleos/sonata-ckeditor-bundle/d/daily)](https://packagist.org/packages/nucleos/sonata-ckeditor-bundle)

[![Continuous Integration](https://github.com/nucleos/SonataCKEditorBundle/workflows/Continuous%20Integration/badge.svg?event=push)](https://github.com/nucleos/SonataCKEditorBundle/actions?query=workflow%3A"Continuous+Integration"+event%3Apush)
[![Code Coverage](https://codecov.io/gh/nucleos/SonataCKEditorBundle/graph/badge.svg)](https://codecov.io/gh/nucleos/SonataCKEditorBundle)
[![Type Coverage](https://shepherd.dev/github/nucleos/SonataCKEditorBundle/coverage.svg)](https://shepherd.dev/github/nucleos/SonataCKEditorBundle)

Symfony Bundle that allows sonata media management for the CKEditor.

Documentation
-------------

* [Installation](#installation)
* [Configuration](#configuration)

## Installation

**1.**  Add dependency with Composer

```bash
composer require nucleos/sonata-ckeditor-bundle
```

**2.** Enable the bundle for all Symfony environments:

```php
// bundles.php
return [
    //...
    Nucleos\SonataCKEditorBundle\SonataCKEditorBundle::class => ['all' => true],
];
```

## Configuration

```yaml
sonata_ck_editor:
    templates:
        browser: '@SonataCKEditor/browser.html.twig'
        upload: '@SonataCKEditor/upload.html.twig'
    autoconfig: # Can be disabled
        contexts: [default]
        browseRoute: admin_app_media_media_ckeditor_browse
        uploadRoute: admin_app_media_media_ckeditor_upload
        fileProvider: sonata.media.provider.file
        imageProvider: sonata.media.provider.image
        imageContext: default
        imageFormat: null
```
