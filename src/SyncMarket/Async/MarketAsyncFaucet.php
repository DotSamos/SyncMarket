<?php 

declare(strict_types=1);

namespace SyncMarket\Async;

use pocketmine\scheduler\AsyncTask;
use SyncMarket\Log\Log;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Queue\Shop\ShopProduct;
use SyncMarket\SyncMarketPlugin;
use SyncMarket\Utils\EndPoint;

/**
 * Um utilitário para ajudar com as requisições
 * 
 * CASO esteja procurando uma maneira de interagir com o sistema de compras dê uma olhada em `SyncMarket/Events`
 * e também em `SyncMarket/Queue/Queue.php`, lá vai provavelmente achar o que procura, no entanto caso não encontrar
 * já lhe deixar alertado que não é possível pegar o resultado das requisições por aqui, o sistema não foi feito
 * para você fazer as requisições diretamente, você deve pegar elas em cache que o sistema proporciona.
 */
class MarketAsyncFaucet {

    /** @var int */
    private static $totalRequestsPerMinute = 0;

    /** @var float */
    private static $lastRequestTime = 0;

    /** @return SyncMarketPlugin */
    public static function getPlugin(): SyncMarketPlugin {
        return SyncMarketPlugin::getInstance();
    }

    /** @param AsyncTask */
    public static function toWorker(AsyncTask $task) {
        if(microtime(true) - self::$lastRequestTime > 60*5) {
            Log::$debug->info('[Requests] Total de requisições nestes ultimos 5 minutos: '.number_format(self::$totalRequestsPerMinute));
            self::$totalRequestsPerMinute = 0;
            self::$lastRequestTime = microtime(true);
        }
        if(self::$lastRequestTime <= 0) self::$lastRequestTime = microtime(true);

        self::getPlugin()->getServer()->getAsyncPool()->submitTask($task);
        self::$totalRequestsPerMinute++;
    }

    public static function validateToken() {
        Log::$debug->debug('Tentando validar o token...');
        self::toWorker(new CheckTokenAsync(
            EndPoint::getToken(),
            EndPoint::parseValidateToken(),
            MarketAsyncTask::METHOD_GET
        ));
    }

    /** @param ShopProduct $product */
    public static function activeProduct(ShopProduct $product) {
        self::toWorker(new ActiveProductAsync(
            $product->getUuid(),
            EndPoint::getToken(),
            EndPoint::parseActive($product->getNickName(), $product->getUuid()),
            MarketAsyncTask::METHOD_PUT,
        ));
    }

    /** @param QueuePlayer $qp */
    public static function updateData(QueuePlayer $qp) {
        self::toWorker(new UpdateDataAsync(
            EndPoint::getToken(),
            EndPoint::parseGetBuy($qp->getName()),
            MarketAsyncTask::METHOD_GET,
            $qp
        ));
    }
}