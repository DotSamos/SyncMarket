<?php 

declare(strict_types=1);

namespace SyncMarket\Queue;

/**
 * Como já deve ter visto na configuração os jogadores são separados em grupos, e estes são atualizados a cada x minutos. Isto
 * foi feito para tentar diminuir o tempo de requisição para assim dar a impressão de uma velocidade maior da ativação dos
 * produtos, e isto aumenta consideravelmente o desempenho do servidor.
 * 
 * Por esta classe você pode interagir com os jogadores que pertencem a esse grupo e claro, adicionar ou remove-los daqui
 */
class QueueGroup {

    /** @var int */
    protected $maxPlayers;

    /** @var QueuePlayer[] */
    protected $players = [];

    /** @param int $maxPlayers */
    public function __construct(int $maxPlayers) {
        $this->maxPlayers = $maxPlayers;
    }

    public function onUpdate() {
        foreach($this->players as $qp) {
            $qp->onUpdate();
        }
    }

    /** @return int */
    public function count(): int {
        return count($this->players);
    }

    /** @return bool */
    public function isFull(): bool {
        return count($this->players) >= $this->maxPlayers;
    }

    /** @return QueuePlayer[] */
    public function getAll(): array {
        return $this->players;
    }

    /** @param QueuePlayer $player */
    public function put(QueuePlayer $player) {
        $this->players[$player->getName()] = $player;
    }

    /** @return QueuePlayer|null */
    public function get(string $name): ?QueuePlayer {
        return $this->players[$name] ?? null;
    }

    /** @param string $name */
    public function remove(string $name) {
        unset($this->players[$name]);
    }

    /** @param array $all */
    public function set(array $all) {
        foreach($all as $qp) {
            $this->put($qp);
        }
    }
}