<?php 

declare(strict_types=1);

namespace SyncMarket\Event;

use pocketmine\event\Event;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Queue\Shop\ShopProduct;

/**
 * Abstração para eventos que incluem um jogador e um produto
 */
class ProductEvent extends Event {
    
    /** @var QueuePlayer */
    protected $player;

    /** @var ShopProduct */
    protected $product;

    /**
     * @param QueuePlayer $player
     * @param ShopProduct $product
     */
    public function __construct(QueuePlayer $player, ShopProduct $product) {
        $this->player = $player;
        $this->product = $product;
    }

    /** @return QueuePlayer */
    public function getPlayer(): QueuePlayer {
        return $this->player;
    }

    /** @return ShopProduct */
    public function getProduct(): ShopProduct {
        return $this->product;
    }
}