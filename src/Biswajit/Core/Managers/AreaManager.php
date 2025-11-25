<?php

namespace Biswajit\Core\Managers;

use Biswajit\Core\API;
use Biswajit\Core\Player;
use Biswajit\Core\Utils\Utils;
use pocketmine\math\AxisAlignedBB;
use pocketmine\world\World;

class AreaManager
{
    use ManagerBase;

    public static function discovery(Player $player): void
    {
        $pos = $player->getPosition();

        $mine = new AxisAlignedBB(13.00, (float) World::Y_MIN, 112.00, 255.00, (float) World::Y_MAX, 461.00);
        $forest = new AxisAlignedBB(-231.00, (float) World::Y_MIN, -554.00, 26.00, (float) World::Y_MAX, -120.00);
        $farming = new AxisAlignedBB(-256.00, World::Y_MIN, -95.00, -42.00, World::Y_MAX, 52.00);
        $gravyad = new AxisAlignedBB(89.00, World::Y_MIN, -94.00, 449.00, World::Y_MAX, 83.00);

        if ($mine->isVectorInXZ($pos)) {

            if (in_array("mine", $player->getArea("area"))) {
                return;

            }
            $player->addArea("area", "mine");
            $player->sendTitle("§l§bMine", "§aNew Zone Discovered!");
            Utils::playSound($player, "camera.take_picture", 1, 3);
            return;
        }

        if ($forest->isVectorInXZ($pos)) {

            if (in_array("forest", $player->getArea("area"))) {
                return;
            }

            $player->addArea("area", "forest");
            $player->sendTitle("§l§bForest", "§aNew Zone Discovered!");
            Utils::playSound($player, "camera.take_picture", 1, 3);
            return;
        }

        if ($farming->isVectorInXZ($pos)) {

            if (in_array("farming", $player->getArea("area"))) {
                return;
            }

            $player->addArea("area", "farming");
            $player->sendTitle("§l§bFarming", "§aNew Zone Discovered!");
            Utils::playSound($player, "camera.take_picture", 1, 3);
            return;
        }

        if ($gravyad->isVectorInXZ($pos)) {

            if (in_array("gravyad", $player->getArea("area"))) {
                return;
            }

            $player->addArea("area", "gravyad");
            $player->sendTitle("§l§bGravyad", "§aNew Zone Discovered!");
            Utils::playSound($player, "camera.take_picture", 1, 3);
            return;
        }

        if ($player->getWorld()->getFolderName() === API::getHub()) {

            if (in_array("village", $player->getArea("area"))) {
                return;
            }
            
            $player->addArea("area", "village");
            $player->sendTitle("§l§bVillage", "§aNew Zone Discovered!");
            Utils::playSound($player, "camera.take_picture", 1, 3);
            return;
        }
    }
}
