<?php

namespace Biswajit\Core\Menus\items;

use Biswajit\Core\API;
use Biswajit\Core\Menus\bank\BankMenu;
use Biswajit\Core\Menus\island\IslandOptionsForm;
use Biswajit\Core\Menus\island\NoIslandForm;
use Biswajit\Core\Menus\settings\SettingsForm;
use Biswajit\Core\Menus\trade\TradeForm;
use Biswajit\Core\Menus\warp\WarpForm;
use Biswajit\Core\Player;
use Biswajit\Core\Sessions\IslandData;
use Biswajit\Core\Utils\Utils;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player as PlayerPlayer;
use pocketmine\Server;
use pocketmine\world\World;

class SkyblockMenu extends MenuForm
{
    public function __construct(Player $player)
    {
        $item = $player->getInventory()->getItemInHand();
        $damage = $item->getAttackPoints();
        $defense = $player->getDefense() + $player->getArmorPoints();
        $heal = $player->getHealth();
        $maxheal = $player->getMaxHealth();
        $name = $player->getName();
        parent::__construct("§fSkyblock Menu", "§bHello,\n§e$name\n\n§d§lSTATS:§r\n\n§cHealth: $heal" . "§7/§c$maxheal \n§aDefense: §a$defense \n§4Damage: $damage \n§r", [
            new MenuOption("§eSKYBLOCK MENU", new FormIcon("textures/icon/island", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eSHOP MENU", new FormIcon("textures/icon/shop", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eKIT MENU", new FormIcon("textures/icon/kits", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eTELEPORT HUB", new FormIcon("textures/icon/hub", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eTRADE MENU", new FormIcon("textures/icon/trade", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eCRAFTING RECIPES", new FormIcon("textures/icon/recipe", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eWARPS", new FormIcon("textures/icon/teleportation", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eBANK", new FormIcon("textures/icon/bank", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§ePLAYER VAULT", new FormIcon("textures/icon/safe", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eBAZAAR", new FormIcon("textures/icon/bazaar", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eSETTINGS", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
        ], function (PlayerPlayer $sender, int $selected): void {
            switch ($selected) {
                case 0:
                    IslandData::get($sender->getName(), function (?IslandData $islandData) use ($sender): void {
                        if ($islandData !== null) {
                            $sender->sendForm(new IslandOptionsForm($sender));
                            return;
                        }
                        $sender->sendForm(new NoIslandForm());
                    });
                    break;
                case 1:
                    //todo
                    break;
                case 2:
                    //todo
                    break;
                case 3:
                    $defaultWorld = Server::getInstance()->getWorldManager()->getWorldByName(API::getHub());
                    if (!$defaultWorld instanceof World) {
                        return;
                    }

                    $sender->teleport($defaultWorld->getSafeSpawn());
                    $sender->sendTitle("§6Welcome To Hub", "" . Utils::getServerName());
                    break;
                case 4:
                    $sender->sendForm(new TradeForm());
                    break;
                case 5:
                    $sender->sendForm(new CraftingTableMenu());
                    break;
                case 6:
                    $sender->sendForm(new WarpForm());
                    break;
                case 7:
                    $sender->sendForm(new BankMenu($sender));
                    break;
                case 8:
                    //todo
                    break;
                case 9:
                    //todo
                    break;
                case 10:
                    $sender->sendForm(new SettingsForm());
                    break;
            }
        });
    }
}
