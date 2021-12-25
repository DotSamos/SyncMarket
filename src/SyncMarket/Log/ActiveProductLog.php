<?php 

declare(strict_types=1);

namespace SyncMarket\Log;

/**
 * Log dos produtos que são ativados
 */
class ActiveProductLog extends Log {

    public const FILE = '/activated_products/{date}.log';

    /** @return true */
    public function canWrite(): bool {
        return $this->getConfig()->generate_activation_log;
    }
}