<?php declare(strict_types=1);

namespace Elio\FastOrder\Core\Content\FastOrder;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class FastOrderEntity extends Entity
{
    use EntityIdTrait;

    protected string $sessionId;

    public function getSessionId() :string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $value): void
    {
        $this->sessionId = $value;
    }
}
