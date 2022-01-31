<?php declare(strict_types=1);

namespace Elio\FastOrder\Core\Content\FastOrderProductLineItem;

use Elio\FastOrder\Core\Content\FastOrder\FastOrderDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class FastOrderProductLineItemDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'fast_order_product_line_item';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return FastOrderProductLineItemEntity::class;
    }

    public function getCollectionClass(): string
    {
        return FastOrderProductLineItemCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new FkField('fast_order_id', 'fastOrderId', FastOrderDefinition::class))->addFlags(new Required()),
            (new FkField('productId', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new IntField('quantity', 'quantity'))->addFlags(new Required()),
            (new IntField('position', 'position'))->addFlags(new Required()),
            (new ManyToOneAssociationField(
                'fastOrder',
                'fast_order_id',
                FastOrderDefinition::class,
                'id',
                false
            ))->addFlags(new Required(), new CascadeDelete()),
            (new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class,
                'id',
                false
            ))->addFlags(new Required())

        ]);
    }
}