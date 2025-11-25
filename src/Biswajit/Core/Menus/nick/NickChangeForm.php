<?php

namespace Biswajit\Core\Menus\nick;

use Biswajit\Core\Skyblock;
use Biswajit\Core\Utils\Utils;
use pocketmine\player\Player;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\CustomFormResponse;

class NickChangeForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct(
            "§6§l«§r §eNickname Menu §6§l»§r",
            [
                new Input("element0", "§6Type the nickname that u want here:", "§7Nickname...", "reset")
            ],
            function (Player $player, CustomFormResponse $response): void {
                $nickName = $response->getString("element0");

                if ($nickName == "reset") {
                    (new Utils())->resetNick($player);
                }
                if (strlen($nickName) > 15) {
                    $player->sendMessage(Skyblock::$prefix . "§r§bYou Can Make Nickname Only In Less Than 9 Characters!");
                    return;
                }
                $player->setDisplayName($nickName);
                $player->setNameTag($nickName);
                $player->sendMessage(" §7Your nickname is now §c" . $nickName);
            }
        );
    }
}
