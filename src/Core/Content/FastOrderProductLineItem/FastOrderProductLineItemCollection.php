<?php declare(strict_types=1);

namespace Elio\FastOrder\Core\Content\FastOrderProductLineItem;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;


/**
 * @method void              add(FastOrderProductLineItemEntity $entity)
 * @method void              set(string $key, FastOrderProductLineItemEntity $entity)
 * @method FastOrderProductLineItemEntity[]    getIterator()
 * @method FastOrderProductLineItemEntity[]    getElements()
 * @method FastOrderProductLineItemEntity|null get(string $key)
 * @method FastOrderProductLineItemEntity|null first()
 * @method FastOrderProductLineItemEntity|null last()
 */
class FastOrderProductLineItemCollection extends EntityCollection
{


    protected function getExpectedClass(): string
    {
        return FastOrderProductLineItemEntity::class;
    }
}
