<?php 

declare(strict_types=1);

namespace SyncMarket\Event;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use pocketmine\Server;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Queue\Shop\ShopProduct;

/**
 * Este evento é chamado sempre que o bonus vai ser enviado.
 * Por aqui é possível interagir com a lista de jogadores que vão receber o bônus e claro, setar novamente essa lista
 */
class BonusSendEvent extends ProductEvent implements Cancellable {

    /** @var Player[] */
    protected $players = [];

    /**
     * @param QueuePlayer $player
     * @param ShopProduct $product
     */
    public function __construct(QueuePlayer $player, ShopProduct $product) {
        parent::__construct($player, $product);
        $this->players = Server::getInstance()->getOnlinePlayers();
    }

    /** @return Player[] */
    public function getPlayers(): array {
        return $this->players;
    }

    /** @param Player[] $list */
    public function setPlayers(array $list) {
        $this->players = $list;
    }
}