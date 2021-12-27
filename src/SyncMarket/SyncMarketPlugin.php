<?php 

declare(strict_types=1);

namespace SyncMarket;

use pocketmine\plugin\PluginBase;
use SyncMarket\Async\MarketAsyncFaucet;
use SyncMarket\Log\Log;
use SyncMarket\Queue\Queue;
use SyncMarket\Task\UpdateSystemTask;
use SyncMarket\Utils\Bonus;

/**
 *  ____                           __  __                  _             _   
 * / ___|   _   _   _ __     ___  |  \/  |   __ _   _ __  | | __   ___  | |_ 
 * \___ \  | | | | | '_ \   / __| | |\/| |  / _` | | '__| | |/ /  / _ \ | __|
 *  ___) | | |_| | | | | | | (__  | |  | | | (_| | | |    |   <  |  __/ | |_ 
 * |____/   \__, | |_| |_|  \___| |_|  |_|  \__,_| |_|    |_|\_\  \___|  \__|
 *          |___/   
 * 
 * @author @SamosMC
 * @link https://github.com/SamosMC/SyncMarket
 * @copyright 2021 SyncMarket ltda. CNPJ: 44.466.665/0001-60
 */
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