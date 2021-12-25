<?php 

declare(strict_types=1);

namespace SyncMarket;

use pocketmine\plugin\PluginBase;
use SyncMarket\Async\MarketAsyncFaucet;
use SyncMarket\Log\Log;
use SyncMarket\Queue\Queue;
use SyncMarket\Task\UpdateSystemTask;
use SyncMarket\Utils\Bonus;

class SyncMarketPlugin extends PluginBase {

    /** @var SyncMarketPlugin */
    private static $instance;

    /** @return SyncMarketPlugin */
    public static function getInstance(): SyncMarketPlugin {
        return self::$instance;
    }

    public function onLoad() {
        self::$instance = $this;

        $this->boot();
    }

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new UpdateSystemTask(), 20);
    }

    private function boot() {
        $this->saveDefaultConfig();
        Log::init();
        MarketAsyncFaucet::validateToken();
        Bonus::init();
        Queue::init();
    }
}