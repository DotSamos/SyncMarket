<?php

declare(strict_types=1);

namespace SyncMarket\Async;

use SyncMarket\Event\UpdateShelfEvent;
use SyncMarket\Queue\Queue;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Queue\Shop\ShopProduct;

/**
 * Esta task atualiza os dados em cache do servidor com os dados no servidor remoto de vendas, ele
 * literalmente seta novamente os produtos em cache com os dados atualizados.
 */
class UpdateDataAsync extends MarketAsyncTask {

    /** @var string */
    protected $queuePlayer;

    /**
     * @param string $token
     * @param string $url
     * @param int $method
     */
    public function __construct(string $token, string $url, int $method, QueuePlayer $qp) {
        parent::__construct($token, $url, $method);
        $this->queuePlayer = serialize($qp);
    }

    public function onSuccess() {
        /** @var QueuePlayer */
        $qp = unserialize($this->queuePlayer);
        $data = json_decode($this->resultBody, true);

        $new = [];
        $old = $qp->shelf()->getAll();

        $qp->shelf()->flush();
        foreach($data as $sp) {
            $sp[] = $qp; // pequena gambiarra...
            $new[] = new ShopProduct(...array_values($sp));
        }

        $ev = new UpdateShelfEvent($qp, $old, $new);
        $ev->call();
        if(!$ev->isCancelled()) {
            $new = $ev->getNewList();
        }

        foreach($new as $sp) {
            $qp->shelf()->put($sp);
        }

        Queue::completeUpdate($qp);
    }
}