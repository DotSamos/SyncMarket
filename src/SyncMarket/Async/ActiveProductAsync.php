<?php 

declare(strict_types=1);

namespace SyncMarket\Async;

/**
 * Task para enviar que o produto x foi ativado para o jogador y
 */
class ActiveProductAsync extends MarketAsyncTask {

    /** @var string */
    protected $uuid;

    /**
     * @param string $uuid
     * @param string $token
     * @param string $url
     * @param int $method
     * @param string|null $promise
     */
    public function __construct(string $uuid, string $token, string $url, int $method) {
        $this->uuid = $uuid;
        parent::__construct($token, $url, $method);
    }
}