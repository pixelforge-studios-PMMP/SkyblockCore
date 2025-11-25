<?php

declare(strict_types=1);

namespace Biswajit\Core\Menus\island;

use Biswajit\Core\Player;
use dktapps\pmforms\MenuForm;
use Biswajit\Core\Skyblock;
use dktapps\pmforms\MenuOption;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;

class WeatherSettingsForm extends MenuForm
{
    public function __construct()
    {
        parent::__construct(
            Skyblock::$prefix . "Weather forecast",
            "",
            [
                new MenuOption("Rainy"),
                new MenuOption("Thunder"),
                new MenuOption("Night"),
                new MenuOption("Day")
            ],
            function (Player $player, int $option): void {
                if ($player->getWorld()->getFolderName() === $player->getName()) {
                    switch ($option) {
                        case 0:
                            if (isset($player::$weathers)) {
                                $player->sendMessage(Skyblock::$prefix . "cYou cannot change it until the server is restarted!");
                                return;
                            }
                            $player::$weathers = "rain";
                            $packet = new LevelEventPacket();
                            $packet->eventId = LevelEvent::START_RAIN;
                            $packet->position = null;
                            $packet->eventData = 10000;
                            $player->getNetworkSession()->sendDataPacket($packet);
                            $player->sendMessage(Skyblock::$prefix . "bWeather set to rainy!");
                            break;
                        case 1:
                            if (isset($player::$weathers)) {
                                $player->sendMessage(Skyblock::$prefix . "cYou cannot change it until the server is restarted!");
                                return;
                            }
                            $player::$weathers = "thunder";
                            $packet = new LevelEventPacket();
                            $packet->eventId = LevelEvent::START_THUNDER;
                            $packet->position = null;
                            $packet->eventData = 10000;
                            $player->getNetworkSession()->sendDataPacket($packet);
                            $player->sendMessage(Skyblock::$prefix . "bWeather set to lightning!");
                            break;
                        case 2:
                            $player->getWorld()->setTime(13000);
                            $player->sendMessage(Skyblock::$prefix . "bWeather set to night!");
                            break;
                        case 3:
                            $player->getWorld()->setTime(1000);
                            $player->sendMessage(Skyblock::$prefix . "bWeather set to day!");
                            break;
                    }
                } else {
                    $player->sendMessage(Skyblock::$prefix . "cYou must be on the island to use this feature!");
                }
            }
        );
    }
}
