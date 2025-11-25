<?php

declare(strict_types=1);

namespace Biswajit\Core\Commands\Staff;

use Biswajit\Core\API;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use pocketmine\Server;
use pocketmine\world\WorldCreationOptions;

class MultiWorld extends Command
{
    public function __construct()
    {
        parent::__construct("mw", "Manage multiple worlds with tab completion", "/mw <subcommand> [args]", ["multiworld"]);
        $this->setPermission("staff.mw.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.no-permission"));
            return false;
        }

        if (empty($args)) {
            $this->sendHelp($sender);
            return true;
        }

        $subcommand = strtolower(array_shift($args));

        switch ($subcommand) {
            case "tp":
            case "teleport":
                return $this->handleTeleport($sender, $args);
            case "list":
                return $this->handleList($sender);
            case "load":
                return $this->handleLoad($sender, $args);
            case "unload":
                return $this->handleUnload($sender, $args);
            case "create":
                return $this->handleCreate($sender, $args);
            case "delete":
                return $this->handleDelete($sender, $args);
            case "worlds":
                return $this->handleWorlds($sender);
            case "players":
                return $this->handlePlayers($sender);
            case "info":
                return $this->handleInfo($sender);
            case "help":
            default:
                $this->sendHelp($sender);
                return true;
        }
    }

    private function handleTeleport(CommandSender $sender, array $args): bool
    {
        if (count($args) < 1) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.tp-usage"));
            return false;
        }

        $worldName = $args[0];
        $server = Server::getInstance();
        $worldManager = $server->getWorldManager();

        if (!$worldManager->isWorldGenerated($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-world-not-exist")));
            return false;
        }

        if (!$worldManager->isWorldLoaded($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-world-not-loaded")));
            if (!$worldManager->loadWorld($worldName)) {
                $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-load-failed")));
                return false;
            }
        }

        $world = $worldManager->getWorldByName($worldName);
        if ($world === null) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-get-world-failed")));
            return false;
        }

        if (isset($args[1])) {
            $targetPlayer = $server->getPlayerByPrefix($args[1]);
            if ($targetPlayer === null) {
                $sender->sendMessage(str_replace("{PLAYER}", $args[1], API::getMessage("multiworld.tp-player-not-found")));
                return false;
            }

            $spawn = $world->getSafeSpawn();
            $targetPlayer->teleport($spawn);
            $sender->sendMessage(str_replace(["{PLAYER}", "{WORLD}"], [$targetPlayer->getName(), $worldName], API::getMessage("multiworld.tp-success-other")));
            $targetPlayer->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-success-target")));
            return true;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.console-error"));
            return false;
        }

        $spawn = $world->getSafeSpawn();
        $sender->teleport($spawn);
        $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-success-self")));
        return true;
    }

    private function handleList(CommandSender $sender): bool
    {
        $worldManager = Server::getInstance()->getWorldManager();
        $worlds = $worldManager->getWorlds();

        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.list-title"));

        foreach ($worlds as $world) {
            $name = $world->getFolderName();
            $loaded = $worldManager->isWorldLoaded($name) ? API::getMessage("multiworld.list-loaded") : API::getMessage("multiworld.list-unloaded");
            $players = count($world->getPlayers());
            $sender->sendMessage(str_replace(["{WORLD}", "{STATUS}", "{PLAYERS}"], [$name, $loaded, $players], API::getMessage("multiworld.list-format")));
        }

        return true;
    }

    private function handleLoad(CommandSender $sender, array $args): bool
    {
        if (count($args) < 1) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.load-usage"));
            return false;
        }

        $worldName = $args[0];
        $worldManager = Server::getInstance()->getWorldManager();

        if ($worldManager->isWorldLoaded($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.load-already-loaded")));
            return true;
        }

        if (!$worldManager->isWorldGenerated($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.load-world-not-exist")));
            return false;
        }

        if ($worldManager->loadWorld($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.load-success")));
            return true;
        } else {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.load-failed")));
            return false;
        }
    }

    private function handleUnload(CommandSender $sender, array $args): bool
    {
        if (count($args) < 1) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.unload-usage"));
            return false;
        }

        $worldName = $args[0];
        $worldManager = Server::getInstance()->getWorldManager();

        if (!$worldManager->isWorldLoaded($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.unload-not-loaded")));
            return true;
        }

        $world = $worldManager->getWorldByName($worldName);
        if ($world === null) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.tp-get-world-failed")));
            return false;
        }

        if (count($world->getPlayers()) > 0) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.unload-has-players")));
            return false;
        }

        $worldManager->unloadWorld($world);
        $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.unload-success")));
        return true;
    }

    private function handleCreate(CommandSender $sender, array $args): bool
    {
        if (count($args) < 1) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.create-usage"));
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.create-types"));
            return false;
        }

        $worldName = $args[0];
        $worldType = $args[1] ?? "normal";
        $worldManager = Server::getInstance()->getWorldManager();

        if ($worldManager->isWorldGenerated($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.create-world-exists")));
            return false;
        }

        $options = new WorldCreationOptions();
        switch (strtolower($worldType)) {
            case "flat":
            case "flatworld":
                $options->setGeneratorClass("flat");
                break;
            case "void":
                $options->setGeneratorClass("void");
                break;
            case "normal":
            default:
                $options->setGeneratorClass("normal");
                break;
        }

        if ($worldManager->generateWorld($worldName, $options)) {
            $sender->sendMessage(str_replace(["{WORLD}", "{TYPE}"], [$worldName, $worldType], API::getMessage("multiworld.create-success")));
            return true;
        } else {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.create-failed")));
            return false;
        }
    }

    private function handleDelete(CommandSender $sender, array $args): bool
    {
        if (count($args) < 1) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.delete-usage"));
            return false;
        }

        $worldName = $args[0];
        $worldManager = Server::getInstance()->getWorldManager();

        if (!$worldManager->isWorldGenerated($worldName)) {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.delete-world-not-exist")));
            return false;
        }

        if ($worldManager->isWorldLoaded($worldName)) {
            $world = $worldManager->getWorldByName($worldName);
            if (count($world->getPlayers()) > 0) {
                $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.delete-has-players")));
                return false;
            }
            $worldManager->unloadWorld($world);
        }

        $worldPath = Server::getInstance()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $worldName;
        if (is_dir($worldPath)) {
            $this->deleteDirectory($worldPath);
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.delete-success")));
            return true;
        } else {
            $sender->sendMessage(str_replace("{WORLD}", $worldName, API::getMessage("multiworld.delete-failed")));
            return false;
        }
    }

    private function handleWorlds(CommandSender $sender): bool
    {
        $worldManager = Server::getInstance()->getWorldManager();
        $loadedWorlds = $worldManager->getWorlds();

        // Get all world directories
        $worldsPath = Server::getInstance()->getDataPath() . "worlds";
        $allWorldNames = [];

        if (is_dir($worldsPath)) {
            $directories = scandir($worldsPath);
            foreach ($directories as $directory) {
                if ($directory !== '.' && $directory !== '..' && is_dir($worldsPath . DIRECTORY_SEPARATOR . $directory)) {
                    $allWorldNames[] = $directory;
                }
            }
        }

        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.worlds-title"));

        if (empty($allWorldNames)) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.worlds-no-worlds"));
            return true;
        }

        foreach ($allWorldNames as $worldName) {
            $loaded = $worldManager->isWorldLoaded($worldName) ? API::getMessage("multiworld.worlds-loaded") : API::getMessage("multiworld.worlds-unloaded");
            $world = $worldManager->getWorldByName($worldName);
            $players = $world !== null ? count($world->getPlayers()) : 0;
            $sender->sendMessage(str_replace(["{WORLD}", "{STATUS}", "{PLAYERS}"], [$worldName, $loaded, $players], API::getMessage("multiworld.worlds-format")));
        }

        return true;
    }

    private function handlePlayers(CommandSender $sender): bool
    {
        $server = Server::getInstance();
        $players = $server->getOnlinePlayers();

        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.players-title"));

        if (empty($players)) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.players-no-players"));
            return true;
        }

        foreach ($players as $player) {
            $world = $player->getWorld()->getFolderName();
            $sender->sendMessage(str_replace(["{PLAYER}", "{WORLD}"], [$player->getName(), $world], API::getMessage("multiworld.players-format")));
        }

        return true;
    }

    private function handleInfo(CommandSender $sender): bool
    {
        $server = Server::getInstance();
        $worldManager = $server->getWorldManager();

        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.info-title"));
        $sender->sendMessage(str_replace("{COUNT}", (string)count($worldManager->getWorlds()), API::getMessage("multiworld.info-total-worlds")));
        $sender->sendMessage(str_replace("{COUNT}", (string)count($server->getOnlinePlayers()), API::getMessage("multiworld.info-online-players")));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.info-commands"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-tp"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-list"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-load"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-unload"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-create"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-delete"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-worlds"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-players"));

        return true;
    }

    private function deleteDirectory(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }

    private function sendHelp(CommandSender $sender): void
    {
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-title"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-tp"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-list"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-load"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-unload"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-create"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-delete"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-worlds"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-players"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-info"));
        $sender->sendMessage(Skyblock::$prefix . API::getMessage("multiworld.help-help"));
    }
}
