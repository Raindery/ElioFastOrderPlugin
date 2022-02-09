<?php declare(strict_types=1);

namespace Elio\FastOrder\Core\Content\FastOrderProductLineItem;


use Elio\FastOrder\Core\Content\FastOrder\FastOrderEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class FastOrderProductLineItemEntity extends Entity
{
    use EntityIdTrait;

    protected ?FastOrderEntity $fastOrder = null;
    protected ?ProductEntity $product = null;
    protected int $quantity;
    protected int $position;


    public function getFastOrder():FastOrderEntity
    {
        return $this->fastOrder;
    }

    public function setFastOrder(FastOrderEntity $fastOrder):void
    {
        $this->fastOrder = $fastOrder;
    }


    public function getProduct():ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product):void
    {
        $this->product = $product;
    }

    public function getQuantity():int
    {
        return $this->quantity;
    }

    public function setQuantity(int $value):void
    {
        $this->quantity = $value;
    }

    public function getPosition():int
    {
        return $this->position;
    }

    public function setPosition(int $value):void
    {
        $this->position = $value;
    }
}
