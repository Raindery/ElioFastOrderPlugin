<?php declare(strict_types=1);

namespace Elio\FastOrder\Resources\Snippets\de_DE;

use Shopware\Core\System\Snippet\Files\SnippetFileInterface;

class SnippetFile_de_DE implements SnippetFileInterface
{

    public function getName(): string
    {
        return 'elio-fast-order.de-DE';
    }

    public function getPath(): string
    {
        return __DIR__.'/elio-fast-order.de-DE.json';
    }

    public function getIso(): string
    {
        return 'de-DE';
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
