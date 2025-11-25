<?php

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\API;
use Biswajit\Core\Skyblock;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FlyCommand extends Command
{
    public function __construct()
    {
        parent::__construct("fly", "§eFly");
        $this->setPermission("player.fly.cmd");
        $this->setPermissionMessage(Skyblock::$prefix . "§cThis command is only for §r§c and + users!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {

            if (count($args) !== 1) {
                $sender->sendMessage(TextFormat::YELLOW . "Usage: /fly on/off");
                return false;
            }

            if ($sender->getWorld()->getFolderName() === API::getHub()) {
                $sender->sendMessage(TextFormat::RED . " You Cant Use This In Hub!");
                return false;
            }

            if ($args[0] === "off") {
                $sender->setFlying(false);
                $sender->sendMessage("⩕ §eYour Fly Disabled Now!");
                return true;
            }

            if ($args[0] === "on") {
                $sender->setFlying(true);
                $sender->sendMessage("⩋ §eYour Fly Enabled Now!");
                return true;
            }
        }
        return false;
    }
}
