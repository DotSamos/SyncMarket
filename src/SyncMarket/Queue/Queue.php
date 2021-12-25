<?php

declare(strict_types=1);

namespace SyncMarket\Queue;

use pocketmine\Player;
use SyncMarket\Async\MarketAsyncFaucet;
use SyncMarket\Log\Log;
use SyncMarket\SyncMarketPlugin;

/**
 * O coração do sistema de fila.
 * 
 * Esta classe controla todo o gerenciamento de atualização dos produtos em fila, ela é responsavel por decidir qual e quanto
 * o grupo deve ser atualizado.
 * Por aqui você também pode pesquisar pelo grupo a qual o jogador pertence, pode procurar direto o objeto QueuePlayer de um
 * jogador ou também gerar novos grupos.
 */
class Queue {

    /** @var QueueGroup[] */
    private static $groups = [];

    /** @var int */
    private static $nextUpdateKey = 0;

    /** @var float */
    private static $updateTime;

    /** @var QueueGroup[] */
    private static $updatedQueuePlayers = [];

    /** @var int */
    private static $totalStartedUpdate = 0;

    /** @var float */
    private static $startedUpdateAt = 0;

    public static function init() {
        Log::$debug->debug('[Queue] Iniciando filas de ativação dos produtos...');
        self::generateGroup();
        self::renewUpdate();
        Log::$debug->debug('[Queue] Filas iniciadas');
    }

    public static function onUpdate() {
        if(self::canUpdateGroup()) {
            self::updateGroup();
        }
        foreach(self::$groups as $k => $gp) {
            if($k == self::$nextUpdateKey && self::$startedUpdateAt != 0) continue;
            $gp->onUpdate();
        }
    }

    public static function nextUpdateKey() {
        $next = self::$nextUpdateKey + 1;
        self::$nextUpdateKey = !isset(self::$groups[$next]) ? 0 : $next;
    }

    public static function renewUpdate() {
        self::$startedUpdateAt = 0;
        self::$totalStartedUpdate = 0;
        self::$updateTime = microtime(true);
        self::nextUpdateKey();

        Log::$debug->info('[Queue] Atualização de grupos renovada. O próximo grupo a ser atualizado é o #'.self::$nextUpdateKey);
    }

    /** @return bool */
    public static function canUpdateGroup(): bool {
        $cooldown = SyncMarketPlugin::getInstance()->getConfig()->getNested('async.check_after_minutes');
        #return microtime(true) - self::$updateTime >= ($cooldown * 60);
        return microtime(true) - self::$updateTime >= 20;
    }

    public static function updateGroup() {

        $group = self::$groups[self::$nextUpdateKey];
        if($group->count() < 1) {
            Log::$debug->debug('[Queue] Não existe ninguem no grupo a ser atualizado. Reagendando atualização e passando para o próximo grupo...');
            self::renewUpdate();
            return;
        }

        Log::$debug->debug('[Queue] Agendando atualização dos produtos do grupo...');

        self::$startedUpdateAt = microtime(true);
        foreach($group->getAll() as $qp) {
            $qp->setUpdatingData(true);
            MarketAsyncFaucet::updateData($qp);
            self::$totalStartedUpdate++;
        }
        Log::$debug->info('[Queue] Atualização dos produtos do grupo iniciando...');
    }

    /** @param QueuePlayer $qp */
    public static function completeUpdate(QueuePlayer $qp) {
        self::$updatedQueuePlayers[$qp->getName()] = $qp;
        $qp->setUpdatingData(false);
        if(--self::$totalStartedUpdate <= 0) {
            self::$groups[self::$nextUpdateKey]->set(self::$updatedQueuePlayers);

            $diff = number_format(microtime(true) - self::$startedUpdateAt, 3);
            SyncMarketPlugin::getInstance()->getLogger()->info("Atualização do grupo #".self::$nextUpdateKey." concluida! ({$diff}s)");
            
            Log::$debug->info("[Queue] Atualização dos produtos do grupo finalizada ({$diff}s)");
            self::renewUpdate();
        }
    }

    /** @param Player $player */
    public static function handleJoin(Player $player) {
        if(!self::getGroupByPlayer($player)) {
            Log::$debug->debug("[Queue] Procurando grupo para {$player->getName()}...");
            $qp = new QueuePlayer($player->getName());

            foreach(self::$groups as $k => $g) {
                if(!$g->isFull()) {
                    Log::$debug->info("[Queue] Jogador posicionado no grupo #{$k}");
                    $g->put($qp);
                    return;
                }
            }
            Log::$debug->info('[Queue] O jogador vai ser posicionado em um novo grupo...');
            self::generateGroup()->put($qp);
        }
    }

    /**
     * @param Player|string $player
     * @return string
     */
    private static function toSearchKey($player): string {
        return $player instanceof Player ? $player->getName() : $player;
    }

    /**
     * @param callable $fun
     * @return QueueGroup|null
     */
    private static function query(callable $fun): ?QueueGroup {
        foreach(self::$groups as $group) {
            if($fun($group)) return $group;
        }
        return null;
    }

    /**
     * @param Player|string $player
     * @return QueueGroup|null
     */
    public static function getGroupByPlayer($player): ?QueueGroup {
        $player = self::toSearchKey($player);

        return self::query(function(QueueGroup $g) use($player){
            foreach($g->getAll() as $p) {
                if($player == $p->getName()) return true;
            }
            return false;
        });
    }

    /**
     * @param Player|string $player
     * @return QueuePlayer|null
     */
    public static function getQueuePlayer($player): ?QueuePlayer {
        $player = self::toSearchKey($player);
        $g = self::getGroupByPlayer($player);

        return $g ? $g->get($player) : null;
    }

    /** @return QueueGroup */
    public static function generateGroup(): QueueGroup {
        $max = SyncMarketPlugin::getInstance()->getConfig()->getNested('async.queue_length');
        return self::$groups[] = new QueueGroup($max);
        Log::$debug->info('[Queue] Novo grupo de atualização criado. Total: '.count(self::$groups));
    }
}