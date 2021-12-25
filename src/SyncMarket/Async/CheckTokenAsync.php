<?php 

declare(strict_types=1);

namespace SyncMarket\Async;

use SyncMarket\Log\Log;
use SyncMarket\SyncMarketPlugin;
use SyncMarket\Utils\EndPoint;

/**
 * Task de verificação de token
 */
class CheckTokenAsync extends MarketAsyncTask {

    public function onFail() {
        $this->disablePlugin('Ocorreu um erro ao executar a verificação do token! Desativando...');
        Log::$debug->critical('[Token-Check] Não foi possível verificar o token');
    }

    public function onSuccess() {
        $this->getPlugin()->getLogger()->info("§aToken verificado com sucesso. Conectado a api SyncMarket ".EndPoint::API_VERSION);
        Log::$debug->info('[Token-Check] Token verificado com sucesso');
    }

    /** @param string $message */
    public function disablePlugin(string $message) {
        $pl = $this->getPlugin();
        $pl->getLogger()->warning($message);
        $pl->getServer()->getPluginManager()->disablePlugin($pl);
    }
}