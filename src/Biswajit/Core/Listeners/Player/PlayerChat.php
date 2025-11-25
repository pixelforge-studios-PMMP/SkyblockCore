<?php

declare(strict_types=1);

namespace Biswajit\Core\Listeners\Player;

use Biswajit\Core\Managers\RankManager;
use Biswajit\Core\Player;
use Biswajit\Core\Skyblock;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\player\chat\LegacyRawChatFormatter;

class PlayerChat implements Listener
{
    public function onPlayerChat(PlayerChatEvent $event)
    {
        $msg = $event->getMessage();
        $player = $event->getPlayer();

        if (!$player instanceof Player) {
            return;
        }

        if (!$player->hasPermission("admin.chat")) {
            if (strlen($event->getMessage()) >= 100) {
                $player->sendMessage(" §cYou Can't Type More Than 100 Letters At Once!");
                $event->cancel();
                return;
            }

            $words = explode(" ", $msg);
            foreach ($words as $word) {
                if (in_array($word, Skyblock::$profanities)) {
                    $player->sendMessage(" §cYou Can't Abuse In Chat!");
                    $event->cancel();
                    return;
                }
            }
        }

        if ($player->hasPermission("emoji.chat")) {
            $textReplacer = Skyblock::getInstance()->getEmojis()->get("Emoji");
            $message = $event->getMessage();
            foreach ($textReplacer as $var) {
                $message = str_replace($var["Before"], $var["After"], $message);
            }
        }

        $chatmessage = str_replace("§", "", $message ?? $msg);

        $format = RankManager::getChatFormat(RankManager::getRankOfPlayer($player));
        $finalMessage = str_replace(["&", "{player_name}", "{msg}"], ["§", $player->getDisplayName(), $chatmessage], $format);
        $event->setFormatter(new LegacyRawChatFormatter($finalMessage));
    }
}
