<?php 

declare(strict_types=1);

namespace SyncMarket\Task;

use pocketmine\scheduler\Task;
use SyncMarket\Queue\Queue;

/**
 * Task de atualização do sistema
 */
class UpdateSystemTask extends Task {

    public function onRun(): void {
        Queue::onUpdate(); // atualizar os produtos em cache
    }
}