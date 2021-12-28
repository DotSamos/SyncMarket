<?php 

declare(strict_types=1);

namespace SyncMarket\Utils;

use SyncMarket\SyncMarketPlugin;

/**
 * Cuidado por aqui o0
 * 
 * Esta classe contém os end-points de nossos servidores, não pense em tocar neles, isso pode parecer pouco mas 
 * o plugin pode funcionar mal caso faça bagunça por aqui.
 */
class EndPoint {

    const API_VERSION = 'v1';

    private const VALIDATE_TOKEN = 'https://api.syncmarket.com.br/plugins/{version}/queue/validate';
    private const GET_BUY_LIST = 'https://api.syncmarket.com.br/plugins/{version}/queue/{nick}';
    private const POST_ACTIVE = 'https://api.syncmarket.com.br/plugins/{version}/queue/{nick}/{uuid}';

    /** @return string */
    public static function getToken(): string {
        return SyncMarketPlugin::getInstance()->getConfig()->get('server_token');
    }

    /**
     * @param string $string
     * @return string
     */
    private static function parseVersion(string $string): string {
        return str_replace('{version}', self::API_VERSION, $string);
    }

    /** @return string */
    public static function parseValidateToken(): string {
        return self::parseVersion(self::VALIDATE_TOKEN);
    }

    /**
     * @param string $nick
     * @return string
     */
    public static function parseGetBuy(string $nick): string {
        return self::parseVersion(str_replace('{nick}', $nick, self::GET_BUY_LIST));
    }

    /**
     * @param string $nick
     * @param string $uuid
     * @return string
     */
    public static function parseActive(string $nick, string $uuid): string {
        return self::parseVersion(str_replace(['{nick}', '{uuid}'], [$nick, $uuid], self::POST_ACTIVE));
    }
}