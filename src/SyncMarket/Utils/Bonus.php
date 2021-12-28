<?php

declare(strict_types=1);

namespace SyncMarket\Utils;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\Server;
use SyncMarket\SyncMarketPlugin;

/**
 * Um componente para enviar o bonus para todos os jogadores online após um jogador comprar um produto
 */
class Bonus {

    /** @var object */
    private static $config;

    public static function init() {
        self::$config = (object)SyncMarketPlugin::getInstance()->getConfig()->get('bonus');
    }

    /** @return object */
    private static function getConfig(): object {
        return self::$config;
    }

    /**
     * @param string $by
     * @param Player[] $to
     */
    public static function sendForAll(string $by, array $to) {
        if(!self::getConfig()->enable) return;

        self::runForAll($by, $to);
    }

    /**
     * @param string $by
     * @param Player[] $to
     */
    private static function runForAll(string $by, array $to) {
        $message = implode("§r\n", self::getConfig()->global_message);
        $message = str_replace('%player%', $by, $message);

        $sender = new ConsoleCommandSender(Server::getInstance(), new Language(Language::FALLBACK_LANGUAGE));
        foreach($to as $p) {
            $p->sendMessage($message);
            Server::getInstance()->getCommandMap()->dispatch($sender, str_replace('%online%', $p->getName(), self::getConfig()->ex_console_command));
        }
    }
}