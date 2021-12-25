<?php 

declare(strict_types=1);

namespace SyncMarket\Async;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use SyncMarket\SyncMarketPlugin;

use function curl_init;
use CurlHandle;
use Exception;
use SyncMarket\Log\Log;

/**
 * Abstração de task assincrona para requisições 
 */
abstract class MarketAsyncTask extends AsyncTask {

    const METHOD_GET = 0;
    const METHOD_PUT = 1;
    const METHOD_POST = 2;

    const TIMEOUT = 10;

    /** @var string */
    protected $token;

    /** @var string */
    protected $url;

    /** @var int */
    protected $method;

    /** @var CurlHandle|null */
    protected $connection;

    /** @var int|null */
    protected $statusCode;

    /** @var string|null */
    protected $resultBody;

    /**
     * @param string $token
     * @param string $url
     * @param int $method
     */
    public function __construct(string $token, string $url, int $method) {
        $this->token = $token;
        $this->url = $url;
        $this->method = $method;
    }
    
    public function onSuccess() {
        // nas classes filhas
    }

    public function onFail() {
        // nas classes filhas
    }

    /** @return SyncMarketPlugin */
    public function getPlugin(): SyncMarketPlugin {
        return SyncMarketPlugin::getInstance();
    }

    public function onRun() {
        try {
            $this->request();
            $this->setResult(['status' => 'success']);
        } catch(Exception $ex) {
            $this->setResult(['status' => 'error', 'ex' => $ex]);
        }
    }

    /** @param Server $server */
    public function onCompletion(Server $server) {
        $result = $this->getResult();
        $status = $result['status'];

        if($status == 'error') {
            /** @var Exception */
            $ex = $result['ex'];
            SyncMarketPlugin::getInstance()
                ->getLogger()->error("§cErro ao executar requisição para `{$this->url}`");
                
            $this->onFail();
            Log::$debug->error("[RequestAsync] A requisição para `{$this->url}` não pode ser completada devido a um erro:");
            Log::$debug->dumpError($ex);
        } else if($status == 'success') {
            $this->onSuccess();
        }
    }

    public function request() {

        $this->connection = $this->makeBaseConnection();

        switch($this->method) {
            case self::METHOD_GET:
                $this->reqGet();
                break;
            case self::METHOD_POST:
                $this->reqPost();
                break;

            case self::METHOD_PUT:
                $this->reqPut();
                break;
        }
    }

    public function executeRequest() {
        $con = $this->connection;
        $result = curl_exec($con);
        if(!$result) throw new Exception('Requisição falha: '.curl_error($con));

        $httpCode = curl_getinfo($con, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($con, CURLINFO_HEADER_SIZE);
        $body = substr($result, $headerSize);

        $this->statusCode = $httpCode;
        $this->resultBody = $body;

        if(!in_array($httpCode, [200, 201, 204])) $this->handleCode($httpCode);

        curl_close($con);
        $this->connection = null;
    }

    /** @param int $code */
    public function handleCode(int $code) {
        $ex = null;

        switch($code) {
            case 0:
                $ex = 'Servidor sem acesso a internet';
                break;
            case 401:
                $ex = 'Token inválido ou conexão não autorizada. Gere ou verifique seu token em <https://app.syncmarket.com.br/servers>';
                break;
            case 500:
                $ex = 'OOPS! Nosso serviço de ativação automática se encontra offline, tente religar o servidor. Caso o erro persistir contate nossa equipe em <https://syncmarket.com.br>';
                break;
            default:
                $ex = "Estranho... Código de erro {$code} | A requisição falhou!";
                break;
        }

        throw new Exception($ex);
    }

    /** @return CurlHandle */
    public function makeBaseConnection() {
        $ch = curl_init($this->url);

        if(!$ch) throw new Exception('Falha ao criar sessão curl. Seu servidor não tem suporte as funções necessárias do PHP para completar a ação. Contate o suporte de sua hospedagem ou ative a extensão curl.');

        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0',
            'Content-Type: application/json',
            "Authorization: {$this->token}"
        ];

        curl_setopt_array($ch, [
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_AUTOREFERER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT_MS => (int) (self::TIMEOUT * 1000),
			CURLOPT_TIMEOUT_MS => (int) (self::TIMEOUT * 1000),
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_HEADER => true
		]);

        return $ch;
    }

    public function reqGet() {
        $this->executeRequest();
    } 

    public function reqPost() {
        curl_setopt($this->connection, CURLOPT_POST, 1);
        $this->executeRequest();
    }

    public function reqPut() {
        curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, "PUT");
        $this->executeRequest();
    }
}