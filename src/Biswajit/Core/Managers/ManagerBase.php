<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Skyblock;
use pocketmine\Server;

trait ManagerBase
{
    protected Skyblock $plugin;

    final protected function __construct()
    {
        if (self::$instance !== null) {
            return;
        }

        self::setInstance($this);
        $this->plugin = Skyblock::getInstance();
        $this->onEnable($this->plugin);
    }


    public function onEnable(): void
    {
    }

    public function onDisable(): void
    {
    }

    /** @var self|null */
    private static $instance = null;

    private static function make(): static
    {
        return new self();
    }

    public static function initialise(): void
    {
        if (self::$instance === null) {
            self::setInstance(self::make());
        }
    }

    public static function isInitialized(): bool
    {
        return self::$instance !== null;
    }

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::setInstance(self::make());
        }
        return self::$instance;
    }

    public static function setInstance(self $instance): void
    {
        self::$instance = $instance;
        Skyblock::getInstance()->addHandler($instance);
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public static function getPlugin(): Skyblock
    {
        return Skyblock::getInstance();
    }

    public static function getServer(): Server
    {
        return Server::getInstance();
    }

    public static function getDataFolder(): string
    {
        return Skyblock::getInstance()->getDataFolder();
    }

}
