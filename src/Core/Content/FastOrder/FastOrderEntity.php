<?php declare(strict_types=1);

namespace Elio\FastOrder\Core\Content\FastOrder;

use Elio\FastOrder\Core\Content\FastOrderProductLineItem\FastOrderProductLineItemCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class FastOrderEntity extends Entity
{
    use EntityIdTrait;

    protected string $sessionId;
    protected ?FastOrderProductLineItemCollection $fastOrderProducts = null;

    public function getSessionId() :string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $value): void
    {
        $this->sessionId = $value;
    }


    public function getFastOrderProducts(): FastOrderProductLineItemCollection
    {
        return $this->fastOrderProducts;
    }

    public function setFastOrderProducts(FastOrderProductLineItemCollection $value):void
    {
        $this->fastOrderProducts = $value;
    }

}
