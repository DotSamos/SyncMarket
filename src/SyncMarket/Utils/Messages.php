<?php 

declare(strict_types=1);

namespace SyncMarket\Utils;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use SyncMarket\SyncMarketPlugin;

/**
 * Um componente para auxilia na hora das mensagens globais de quanto um produto é comprado na loja.
 * Esta classe também controla a mensagem em action-bar que o dono do produto vê quando o produto é ativado
 */
class Messages {

    /** @return Config */
    private static function getConfig(): Config {
        return SyncMarketPlugin::getInstance()->getConfig();
    }    

    /** @return Player[] */
    private static function getOnlinePlayers(): array {
        return Server::getInstance()->getOnlinePlayers();
    }

    /** @param string $playerName */
    public static function broadcastBuy(string $playerName) {
        $config = self::getConfig();

        if(!$config->getNested('activation_notification.enable')) return;

        $formatMessage = function(string $str) use($playerName): string {
            return str_replace('%player%', $playerName, $str);
        };

        $method = null;
        $args = [];
        switch($config->getNested('activation_notification.notification_mode')) {
            case 0:
                $method = 'addTitle';
                $args = array_map($formatMessage, $config->getNested('activation_notification.notification_title'));
                break;
            case 1:
            default:
                $method = 'sendMessage';
                $args = $formatMessage(implode("§r\n", $config->getNested('activation_notification.notification_message')));
                break;
        }
        
        foreach(self::getOnlinePlayers() as $p) {
            if(is_array($args)) {
                call_user_func([$p, $method], ...array_values($args));
            } else {
                call_user_func([$p, $method], $args);
            }
        }
    }

    /** @param Player $p */
    public static function sendProductActivedOwner(Player $p) {
        $p->sendActionBarMessage(self::getConfig()->getNested('activation_notification.owner_notification'));
    }
}