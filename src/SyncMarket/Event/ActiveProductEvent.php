<?php 

declare(strict_types=1);

namespace SyncMarket\Event;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use SyncMarket\Queue\QueuePlayer;
use SyncMarket\Queue\Shop\ShopProduct;

/**
 * Este evento é chamado quando um produto vai ser ativado para um jogador.
 * 
 * O jogador fica encapsulado na classe QueuePlayer que contém métodos básicos sobre a "conta" dele em nosso sistema de shop,
 * e já o produto a ser ativado é representado pela classe ShopProduct, por ela é possível pegar todas as informações do
 * produto.
 * 
 * Este evento pode ser cancelado, no entanto a menos que você o remova da lista de produtos (ou prateleira como chamei) este
 * produto vai tentar ser ativado e por consequencia invocar o comando novamente a cada 1 segundo que é o tempo de verificação
 * de ativação do produto em cache.
 */
class ActiveProductEvent extends ProductEvent implements Cancellable {

}