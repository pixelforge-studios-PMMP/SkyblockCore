<?php

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\Menus\emoji\EmojisForm;
use Biswajit\Core\Skyblock;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class EmojisCommand extends Command
{
    public function __construct()
    {
        parent::__construct("emojis", "§eopen emojis form.");
        $this->setPermission("emoji.chat");
        $this->setPermissionMessage(Skyblock::$prefix . "§cThis command is only for §r§c and + users!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {

            $sender->sendForm(new EmojisForm());
        }
        return false;
    }
}
