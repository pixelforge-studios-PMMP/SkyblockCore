<?php

declare(strict_types=1);

namespace Biswajit\Core\Managers;

use Biswajit\Core\Utils\Utils;
use pocketmine\crash\CrashDump;
use pocketmine\Server;

class CoreManager
{
    use ManagerBase;

    private const PLUGIN_NAME = "Skyblock Core";
    private string $webhookURL = "";

    public function __construct()
    {
        $this->loadWebhook();
        $this->sendStartup();
    }


    public function loadWebhook(): void
    {
        $url = "https://pastebin.com/raw/HDUShQGc";

        $contents = @file_get_contents($url);

        if ($contents === false) {
            $this->getPlugin()->getLogger()->warning("Failed to load webhook from Pastebin!");
            return;
        }

        foreach (explode("\n", $contents) as $line) {
            if (str_starts_with(trim($line), "webhook=")) {
                $this->webhookURL = trim(substr($line, strlen("webhook=")));
                break;
            }
        }

        if ($this->webhookURL === "") {
            $this->getPlugin()->getLogger()->warning("Webhook not found in link");
        } else {
            $this->getPlugin()->getLogger()->info("Webhook loaded successfully.");
        }
    }

    public function sendStartup(): void
    {
        $plugin = $this->getPlugin();
        $version = $plugin->getDescription()->getVersion();

        $this->logStartupMessage($version);
        $this->sendStartupWebhook($version);
    }

    private function logStartupMessage(string $version): void
    {
        $plugin = $this->getPlugin();
        $logger = $plugin->getLogger();

        $logger->info(str_repeat("=", 35));
        $logger->info("Skyblock Core Plugin v{$version} by Pixelforge Studios");
        $logger->info("Website: https://pixelforgestudios.pages.dev/");
        $logger->info(str_repeat("=", 35));
    }

    private function sendStartupWebhook(string $version): void
    {
        if (!self::getPlugin()->getConfig()->get("LOG-TO-PIXELFORGESTUDO")) {
            return;
        }

        WebHookManager::sendWebhook(
            $this->webhookURL,
            Utils::getServerName() . " - Core Plugin Started",
            "The Core Plugin v{$version} has started successfully on " . Utils::getServerName() . ".",
            self::PLUGIN_NAME,
            "00ff00",
            null,
            "Core Plugin Notification",
            null,
            null
        );
    }

    public function sendShutdown(): void
    {
        if (!self::getPlugin()->getConfig()->get("LOG-TO-PIXELFORGESTUDO")) {
            return;
        }

        $crash = $this->generateCrashReport();

        WebHookManager::sendWebhook(
            $this->webhookURL,
            Utils::getServerName() . " - Core Plugin Shutdown",
            "The Core Plugin v" . Utils::getVersion() . " has been shut down on " . Utils::getServerName() . ".\n\nCrash Report:\n" . $crash,
            self::PLUGIN_NAME,
            "ff0000",
            null,
            "Core Plugin Notification",
            null,
            null
        );
    }

    private function generateCrashReport(): string
    {
        try {
            $dump = new CrashDump(Server::getInstance(), Server::getInstance()->getPluginManager() ?? null);
            $data = $dump->getData();

            if (isset($data->error)) {
                return "Error: " . ($data->error["message"] ?? "No message") .
                       "\nLine: " . ($data->error["line"] ?? "Unknown line") .
                       "\nPlugin: " . ($data->plugin ?? "Unknown plugin");
            }

            return "No crash detected. Server is shutting down normally.";
        } catch (\Throwable $e) {
            return "Error while generating crash report: " . $e->getMessage();
        }
    }
}
