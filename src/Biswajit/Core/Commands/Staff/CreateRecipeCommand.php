<?php

declare(strict_types=1);

namespace Biswajit\Core\Commands\Staff;

use Biswajit\Core\Menus\crafting\CustomCraftingMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateRecipeCommand extends Command
{
    public function __construct()
    {
        parent::__construct("createrecipe", "create a new recipe", "/cr <name>", ["cr"]);
        $this->setPermission("staff.craft.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game!");
            return false;
        }

        if (!$this->testPermission($sender)) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command!");
            return false;
        }

        if (count($args) !== 1) {
            $sender->sendMessage(TextFormat::YELLOW . "Usage: /cr <name>");
            return false;
        }

        CustomCraftingMenu::createRecipe($sender, $args[0]);

        return true;
    }
}
