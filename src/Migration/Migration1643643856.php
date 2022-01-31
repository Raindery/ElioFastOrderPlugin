<?php declare(strict_types=1);

namespace Elio\FastOrder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1643643856 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1643643856;
    }

    public function update(Connection $connection): void
    {
        $queryFastOrder = <<<SQL
CREATE TABLE IF NOT EXISTS `fast_order` (
     `id`           BINARY(16)              NOT NULL,
     `session_id`   BINARY(16)              NOT NULL,
     `created_at`   DATETIME(3)             NOT NULL,
     `updated_at`   DATETIME(3)             NULL,
     PRIMARY KEY (`id`)
 )
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;


        $queryFastOrderProductLineItem = <<<SQL
CREATE TABLE IF NOT EXISTS `fast_order_product_line_item` (
    `id`            BINARY(16)          NOT NULL,
    `fast_order_id` BINARY(16)          NOT NULL,
    `product_id`    BINARY(16)          NOT NULL,
    `quantity`      INTEGER             NOT NULL,
    `position`      INTEGER             NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.fast_order_product_line_item.fast_order_id` FOREIGN KEY (`fast_order_id`)
     REFERENCES `fast_order` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `fk.fast_order_product_line_item.product_id` FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
)
ENGINE = InnoDB DEFAULT CHARSET utf8mb4 COLLATE = utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($queryFastOrder);
        $connection->executeStatement($queryFastOrderProductLineItem);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
