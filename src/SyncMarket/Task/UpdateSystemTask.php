<?php 

declare(strict_types=1);

namespace SyncMarket\Task;

use pocketmine\scheduler\Task;
use SyncMarket\Queue\Queue;

/**
 * Task de atualização do sistema
 */
class UpdateSystemTask extends Task {

    /** @param int $currentTick */
    public function onRun(int $currentTick) {
        Queue::onUpdate(); // atualizar os produtos em cache
    }
}