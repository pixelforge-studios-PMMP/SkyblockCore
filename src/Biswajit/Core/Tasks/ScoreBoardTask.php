<?php

namespace Biswajit\Core\Tasks;

use Biswajit\Core\API;
use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Managers\ScoreBoardManager;
use Biswajit\Core\Skyblock;
use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;

class ScoreBoardTask extends Task
{
    private string $timezone = "Asia/Kolkata";
    private string $serverIp;
    private array $scoreboardTitles;

    public function __construct()
    {
        $plugin = Skyblock::getInstance();
        $this->serverIp = $plugin->getConfig()->get("SERVER-IP");
        $this->scoreboardTitles = $plugin->getConfig()->get("SCOREBOARD-TITLES");
        date_default_timezone_set($this->timezone);
    }

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $this->updateScoreboard($player);
        }
    }

    private function updateScoreboard(Player $player): void
    {
        $data = [
            'playername' => $player->getName(),
            'balance' => EconomyManager::getMoney($player),
            'gems' => EconomyManager::getGems($player),
            'date' => date("d"),
            'time' => date("h:i"),
            'area' => API::getPlayerWorld($player)
        ];

        $oldTitle = ScoreBoardManager::getScoreboard();
        $newTitle = $this->scoreboardTitles[$oldTitle];
        ScoreBoardManager::setScoreboard($newTitle);

        ScoreBoardManager::removeScoreboard($player, "ScoreBoard");
        ScoreBoardManager::createScoreboard($player, "    §r  §l{$newTitle}    ", "ScoreBoard");

        $lines = [
            "§r",
            "§7Mid Summer {$data['date']}th",
            "§r  §r{$data['time']}",
            "§b §c §r",
            "§r  §a{$data['playername']}",
            "§a §e §r",
            "§r ⩐ Purse: §e{$data['balance']}",
            "§r ⨝ Gems: §e{$data['gems']}",
            "§8 §r",
            "§r §a{$data['area']}   ",
            "§9          §r",
            "§r§3{$this->serverIp} "
        ];

        foreach ($lines as $index => $line) {
            ScoreBoardManager::setScoreboardEntry($player, $index, $line, "ScoreBoard");
        }
    }
}
