<?php 

declare(strict_types=1);

namespace SyncMarket\Log;

use DateTime;
use DateTimeZone;
use Exception;
use pocketmine\utils\Utils;
use SyncMarket\SyncMarketPlugin;

/**
 * Abstração das logs
 * 
 * Esta classe controla o sistema de criação e escrita das logs, e também é por aqui que deixamos
 * objetos unicos instanciados para serem utilizados pelo sistema.
 * 
 * PS: Sei que isso não segue os padrões da PSR, mas adaptei a maioria que usei no sistema :v
 */
abstract class Log {

    public const FILE = null;

    # ------------------------------

    /** @var DebugLog */
    public static $debug;

    /** @var ActiveProductLog */
    public static $acProduct;

    public static function init() {
        self::$debug = new DebugLog();
        self::$acProduct = new ActiveProductLog();
    }

    # ------------------------------

    /** @var resource */
    protected $resource;

    /** @var string */
    protected $generatedAt;

    public function __construct() {
        if(!static::FILE) throw new Exception('static::FILE not can be null!');
        $this->prepareResource();
    }

    /** @return object */
    public function getConfig(): object {
        return (object)SyncMarketPlugin::getInstance()->getConfig()->get('logs');
    }

    /** @return string */
    public function getFilePath(): string {
        return str_replace(
            '{date}',
            date('d.m.Y'),
            SyncMarketPlugin::getInstance()->getDataFolder().'logs/'.static::FILE
        );
    }

    /** @return true */
    public function canWrite(): bool {
        return true;
    }

    public function checkResource() {
        if($this->generatedAt != date('d/m/Y')) {
            $this->info('Novo dia detectado. Gerando novo arquivo de log...');
            $this->close();
            $this->prepareResource();
        }
    }

    private function prepareResource() {

        if(!$this->canWrite()) return;

        $file = $this->getFilePath();
        $path = dirname($file);
        if(!file_exists($path)) mkdir($path, 0755, true);

        $rs = fopen($file, 'a');
        if(!$rs) throw new Exception("Failed on create log `{$file}`");

        $this->resource = $rs;
        $this->generatedAt = date('d/m/Y');

        $this->cutter();
    }

    private function cutter() {
        $this->writeLine(str_repeat('-', 90));
    }

    /** @param string $text */
    private function writeLine(string $text) {

        if(!$this->canWrite()) return;

        $this->checkResource();

        $time = (new DateTime('now', new DateTimeZone($this->getConfig()->time_zone)))->format('d/m/Y|H:i:s');
        $text = wordwrap("\n[{$time}]{$text}", 120);
        fwrite($this->resource, $text);
    }

    public function close() {
        $this->cutter();
        fclose($this->resource);
    }

    /** @param string $message */
    public function info(string $message) {
        $this->writeLine("[INFO] {$message}");        
    }

    /** @param string $message */
    public function alert(string $message) {
        $this->writeLine("[ALERT] {$message}");
    }

    /** @param string $message */
    public function critical(string $message) {
        $this->writeLine("[CRITICAL] {$message}");
    }

    /** @param string $message */
    public function error(string $message) {
        $this->writeLine("[ERROR] {$message}");
    }

    /** @param string $message */
    public function debug(string $message) {
        $this->writeLine("[DEBUG] {$message}");
    }

    /** @param Exception $ex */
    public function dumpError(Exception $ex) {
        $message = [
            "({$ex->getCode()}) {$ex->getMessage()}",
            Utils::cleanPath($ex->getFile()).' @ '.$ex->getLine(),
        ];
        $this->cutter();
        $this->error(implode("\n", $message));
        $this->cutter();
    }
}