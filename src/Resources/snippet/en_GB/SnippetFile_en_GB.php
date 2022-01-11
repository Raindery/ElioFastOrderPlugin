<?php declare(strict_types=1);

namespace Elio\FastOrder\Resources\Snippets\en_GB;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_en_GB implements SnippetFileInterface
{

    public function getName(): string
    {
        return 'elio-fast-order.en-GB';
    }

    public function getPath(): string
    {
        return __DIR__.'/elio-fast-order.en-GB.json';
    }

    public function getIso(): string
    {
        return 'en-GB';
    }

    public function getAuthor(): string
    {
        return 'ElioFastOrder';
    }

    public function isBase(): bool
    {
        return false;
    }
}