<?php

namespace Biswajit\Core\Menus\emoji;

use Biswajit\Core\Skyblock;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Label;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class EmojisForm extends CustomForm
{
    public function __construct()
    {
        $emojisConfig = new Config(Skyblock::getInstance()->getDataFolder() . "emojis.yml", Config::YAML);
        $emojis = $emojisConfig->get("Emoji", []);
        $description = "§e";
        foreach ($emojis as $emoji) {
            $before = $emoji["Before"];
            $after = $emoji["After"];
            $description .= $before . " : " . $after . "\n\n";
        }
        $elements = [
            new Label("emojis", $description)
        ];
        parent::__construct(
            "§l§eChat Emojis",
            $elements,
            function (Player $player, $response): void {
                // No action needed for display form
            }
        );
    }
}
