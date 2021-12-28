<?php 

declare(strict_types=1);

namespace SyncMarket;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use SyncMarket\Queue\Queue;

/**
 * Uma listener bem simples não?
 */
class EventListener implements Listener {

    /** @param PlayerJoinEvent $e */
    public function onJoin(PlayerJoinEvent $e) {
        Queue::handleJoin($e->getPlayer());
    }
}