<?php 

declare(strict_types=1);

namespace SyncMarket\Queue\Shop;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\lang\Language;
use pocketmine\Server;
use SyncMarket\Async\MarketAsyncFaucet;
use SyncMarket\Event\ActiveProductEvent;
use SyncMarket\Event\BonusSendEvent;
use SyncMarket\Log\Log;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Utils\Bonus;
use SyncMarket\Utils\Messages;

/**
 * O produto da loja
 * 
 * Está classe controla todo o fluxo de informações de um produto especifico que vai ser ativado ou não para um jogador.
 */
class ShopProduct {

    const ACTIVE_TYPE_ONLINE = 'ONLINE';
    const ACTIVE_TYPE_OFFLINE = 'OFFLINE';

    const STATUS_FORWARDED = 'FORWARDED';
    const STATUS_DELIVERED = 'DELIVERED';
    const STATUS_TIMEOUT = 'TIMEOUT';
    const STATUS_ABORTED = 'ABORTED';

    /** @var string */
    protected $uuid;

    /** @var string */
    protected $nickName;

    /** @var string */
    protected $exCommand;

    /** @var string */
    protected $activeType;

    /** @var string $status */
    protected $status;

    /** @var int|null */
    protected $slotsNeed;

    /** @var bool */
    protected $hasChanged = false;

    /** @var QueuePlayer */
    protected $player;

    /**
     * @param string $uuid
     * @param string $nickName
     * @param string $exCommand
     * @param string $activeType
     * @param string $status
     * @param int|null $slotsNeed
     */
    public function __construct(
        string $uuid,
        string $nickName,
        string $exCommand,
        string $activeType,
        string $status,
        $slotsNeed,
        QueuePlayer $player
    ) {
        $this->uuid = $uuid;
        $this->nickName = $nickName;
        $this->exCommand = $exCommand;
        $this->activeType = $activeType;
        $this->status = $status;
        $this->slotsNeed = $slotsNeed ?? 0;
        $this->player = $player;
    }

    /** @return string */
    public function getUuid(): string {
        return $this->uuid;
    }

    /** @return string */
    public function getNickName(): string {
        return $this->nickName;
    }

    /** @return string */
    public function getExCommand(): string {
        return $this->exCommand;
    }

    /** @return string */
    public function getActiveType(): string {
        return $this->activeType;
    }

    /** @return string */
    public function getStatus(): string {
        return $this->status;
    }

    /** @return string */
    public function getSlotsNeed(): int {
        return $this->slotsNeed;
    }

    /** @return QueuePlayer */
    public function player(): QueuePlayer {
        return $this->player;
    }

    /** @return bool */
    public function canActive(): bool {
        $isOnline = $this->player->isOnline();
        if($this->activeType == self::ACTIVE_TYPE_ONLINE && !$isOnline) return false;
        
        if($this->slotsNeed > 0 && !$isOnline) return false;
        if(!$this->hasTotalEmptySlots()) {
            if(date('s') == '59') 
                Log::$acProduct->info("Aguardando {$this->slotsNeed} slots vazios no inventário de {$this->player->getName()} para realizar a entrega #{$this->uuid}...");
            
            return false;
        }
       
        return $this->status == self::STATUS_FORWARDED;
    }

    /** @return bool */
    public function hasTotalEmptySlots(): bool {
        $totalEmpty = count(
            array_filter($this->player->getPlayer()->getInventory()->getContents(true), function(Item $item){
                return $item->getId() == 0;
            })
        );
        return $this->slotsNeed > 0 ? $totalEmpty >= $this->slotsNeed : true;
    }

    public function active() {

        $ev = new ActiveProductEvent($this->player, $this);
        $ev->call();

        if($ev->isCancelled()) return;

        $log = ['[Product] Ativando produto...'];
        $sender = new ConsoleCommandSender(Server::getInstance(), new Language(Language::FALLBACK_LANGUAGE));
        Server::getInstance()->getCommandMap()->dispatch($sender, $this->exCommand);
        $log[] = " 1. Comando executado (em terminal): {$this->exCommand}";

        $log[] = ' 2. Definindo status do produto para entregue...';
        $this->setDelivered();

        $qp = $this->player;
        Messages::broadcastBuy($qp->getName());
        $log[] = ' 3. Anuncio global realizado';
        if($qp->isOnline()) Messages::sendProductActivedOwner($qp->getPlayer());

        $ev = new BonusSendEvent($this->player, $this);
        $ev->call();

        if(!$ev->isCancelled()) {
            Bonus::sendForAll($qp->getName(), $ev->getPlayers());
            $log[] = ' 4. Bonus enviado para todos os jogadores';
        }

        $log[] = " + Processo de ativação do produto #{$this->uuid} realizado. Produto entregue para {$this->player->getName()}";

        Log::$acProduct->info(implode("\n", $log));
    }

    public function setDelivered() {
        $this->status = self::STATUS_DELIVERED;
        $this->hasChanged = true;
        MarketAsyncFaucet::activeProduct($this);
    }

    public function onUpdate() {
        if(in_array($this->status, [self::STATUS_ABORTED, self::STATUS_DELIVERED])) { // produto já entregue ou compra cancelada
            Log::$acProduct->info("[Product] Status do produto alterado para {$this->status}. Removendo #{$this->uuid} da estante de produtos de {$this->player->getName()}...");
            $this->player->shelf()->remove($this->uuid); 
            return;
        }
        if($this->canActive()) $this->active();
    }

    /** @param array $query */
    public function fill(array $query) {
        $fixIndex = [
            'command' => 'exCommand',
            'type' => 'activeType',
            'status' => 'status',
            'slotsNeeded' => 'slotsNeed'
        ];

        $updated = [];
        foreach($fixIndex as $k => $v) {
            if($query[$k] !== $this->{$v}) {
                $this->__set($v, $query[$k]);
                $updated[] = $k;
            }
        }

        if(!empty($updated)) {
            Log::$acProduct->info("[Product#{$this->uuid}] Detectada alterações do produto nos campos: ".implode(', ', $updated).'. Campos atualizados com base na ultima atualização');
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->{$name} = $value;
        $this->hasChanged = true;
    }
}