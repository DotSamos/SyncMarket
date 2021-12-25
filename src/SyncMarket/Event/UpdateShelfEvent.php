<?php 

declare(strict_types=1);

namespace SyncMarket\Event;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Queue\Shop\ShopProduct;

/**
 * Sempre quando vamos atualizar os produtos em cache de um jogador fazemos essa ação em messa com todos os produtos. Por
 * aqui você pode cancelar o evento (o que vai impedir que o cache dele seja atualizado e fique com os antigos registros) ou
 * pode interagir com os novos produtos e setar ela em seguida.
 */
class UpdateShelfEvent extends Event implements Cancellable {

    /** @var QueuePlayer */
    protected $player;

    /** @var ShopProduct[] */
    protected $oldList;

    /** @var ShopProduct[] */
    protected $newList;

    /**
     * @param QueuePlayer $player
     * @param ShopProduct[] $old
     * @param ShopProduct[] $new
     */
    public function __construct(QueuePlayer $player, array $old, array $new) {
        $this->player = $player;
        $this->oldList = $old;
        $this->newList = $new;
    }

    /** @return QueuePlayer */
    public function getPlayer(): QueuePlayer {
        return $this->player;
    }

    /** @return ShopProduct[] */
    public function getOldList(): array {
        return $this->oldList;
    }

    /** @return ShopProduct[] */
    public function getNewList(): array {
        return $this->newList;
    }

    /** @param ShopProduct[] */
    public function setNewList(array $new) {
        $this->newList = $new;
    }
}