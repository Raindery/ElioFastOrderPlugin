<?php declare(strict_types=1);

namespace Elio\FastOrder\Core\Content\FastOrderProductLineItem;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class FastOrderProductLineItemCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return FastOrderProductLineItemEntity::class;
    }
}
