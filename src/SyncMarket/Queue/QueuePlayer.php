<?php 

declare(strict_types=1);

namespace SyncMarket\Queue;

use pocketmine\player\Player;
use pocketmine\Server;
use SyncMarket\Queue\Shop\ProductShelf;

/**
 * Esta classe controla todas as informações que temos sobre o jogador, desta forma caso procure pelo nome dele ou a lista
 * de produtos que ele tenha comprado em nossa loja está no lugar certo.
 * 
 * Por aqui é possível pegar o objeto default do jogador (no entanto caso ele não esteja online vai retornar null),
 * verificar se o sistema está atualizando os dados dos produtos em cache ou ainda acessar a lista de produtos em cache
 */
class QueuePlayer {

    /** @var string */
    protected $name;

    /** @var ProductShelf */
    protected $shelf;

    /** @var bool */
    protected $isUpdatingData = false;

    /** @param string $name */
    public function __construct(string $name) {
        $this->name = $name;
        $this->shelf = new ProductShelf();
    }

    /** @return string */
    public function getName(): string {
        return $this->name;
    }

    /** @return Player|null */
    public function getPlayer(): ?Player {
        return Server::getInstance()->getPlayerByPrefix($this->name);
    }

    /** @return bool */
    public function isOnline(): bool {
        return (bool)$this->getPlayer();
    }

    /** @return ProductShelf */
    public function shelf(): ProductShelf {
        return $this->shelf;
    }   

    /** @return bool */
    public function isUpdatingData(): bool {
        return $this->isUpdatingData;
    }

    /** @return bool */
    public function setUpdatingData(bool $bool) {
        $this->isUpdatingData = $bool;
    }

    public function onUpdate() {
        if($this->isUpdatingData()) return; // se a data está sendo atualizada algum produto pode ter mudado ou algo do gênero
        $this->shelf->onUpdate();
    }
}