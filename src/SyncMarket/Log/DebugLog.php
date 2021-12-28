<?php 

declare(strict_types=1);

namespace SyncMarket\Log;

/**
 * Log de debug do sistema
 */
class DebugLog extends Log {

    public const FILE = 'debug/{date}.log';

    /** @return bool */
    public function canWrite(): bool {
        return $this->getConfig()->generate_debug_log;
    }
}