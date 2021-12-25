<?php 

declare(strict_types=1);

namespace SyncMarket\Queue\Shop;

/**
 * A prateleira de produtos da loja
 * 
 * Cada QueuePlayer tem sua prateleira com os produtos que ele comprou na loja.
 * Vale lembrar que nem todos os produtos que contiverem aqui já foram devidamente pagos.
 * Aqui devo lhe alertar sobre o UUID do produto, diferente do que pensa o UUID só representa o id unico de produto,
 * então por exemplo, se um jogador x comprou o produto a e o jogador y também comprou o produto a, o produto a tem o
 * mesmo UUID, então tome cuidado em suas verificações.
 */
class ProductShelf {

    /** @var ShopProduct[] */
    protected $products = [];

    public function flush() {
        $this->products = [];
    }

    /** @param ShopProduct */
    public function put(ShopProduct $product) {
        $this->products[$product->getUuid()] = $product;
    }

    /**
     * @param string $uuid
     * @return ShopProduct|null
     */
    public function get(string $uuid): ?ShopProduct {
        return $this->products[$uuid] ?? null;
    }

    /** @return ShopProduct[] */
    public function getAll(): array {
        return $this->products;
    }

    /** @param string $uuid */
    public function remove(string $uuid) {
        unset($this->products[$uuid]);
    }

    public function onUpdate() {
        foreach($this->products as $product) {
            $product->onUpdate();
        }
    }
}