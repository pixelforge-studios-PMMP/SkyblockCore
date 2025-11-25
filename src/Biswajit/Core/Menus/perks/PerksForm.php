<?php

namespace Biswajit\Core\Menus\perks;

use Biswajit\Core\Player;
use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player as PlayerPlayer;

class PerksForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct("§l§5PERKS", "§6Please Select The Next Menu", [
            new MenuOption("§eNight Vision\n§8Click To Use", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§eSpeed\n§8Click To Use", new FormIcon("textures/icon/settings", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("§cExit", new FormIcon("textures/blocks/barrier", FormIcon::IMAGE_TYPE_PATH))
        ], function (PlayerPlayer $sender, int $selected): void {

            if (!$sender instanceof Player) {
                return;
            }

            switch ($selected) {
                case 0:
                    if ($sender->getVision()) {
                        $sender->setVision(false);
                        $sender->getEffects()->remove(VanillaEffects::NIGHT_VISION());
                        $sender->sendMessage(" §aVision: Off");
                        return;
                    }

                    $sender->setVision(true);
                    $sender->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 600 * 100, 3));
                    $sender->sendMessage(" §aVision: On");
                    break;

                case 1:
                    if ($sender->getSpeed()) {
                        $sender->setSpeed(false);
                        $sender->getEffects()->remove(VanillaEffects::SPEED());
                        $sender->sendMessage("§aSpeed: Off");
                        return;
                    }

                    $sender->setSpeed(true);
                    $sender->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 600 * 100, 3));
                    $sender->sendMessage("§aSpeed: On");
                    break;
            }
        });
    }
}
