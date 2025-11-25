<?php

declare(strict_types=1);

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\Menus\bank\BankMenu;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class BankCommand extends Command
{
    public function __construct()
    {
        parent::__construct("bank", "§eopen bank menu");
        $this->setPermission("player.bank.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if ($sender instanceof Player) {
            $sender->sendForm(new BankMenu($sender));
            return true;
        }

        $sender->sendMessage("Use this command in-game");
        return false;
    }
}
