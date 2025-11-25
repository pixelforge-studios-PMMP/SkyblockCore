<?php

declare(strict_types=1);

namespace Biswajit\Core\Commands\player;

use Biswajit\Core\API;
use Biswajit\Core\Skyblock;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class TopBankCommand extends Command
{
    public function __construct()
    {
        parent::__construct("topbank", "Show top players by bank money", "/topbank", ["tbank"]);
        $this->setPermission("player.topbank.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(Skyblock::$prefix . API::getMessage("economy.no-permission"));
            return false;
        }

        Skyblock::getInstance()->getDataBase()->executeSelect("economy.loadAll", [], function (array $rows) use ($sender): void {
            $players = [];
            foreach ($rows as $row) {
                $data = json_decode($row["data"], true);
                $bankMoney = $data["bank-money"] ?? 0;
                $name = $data["name"] ?? "Unknown";
                $players[$name] = $bankMoney;
            }
            arsort($players);
            $message = "§eTop Bank Money Players:\n";
            $count = 0;
            foreach ($players as $name => $bankMoney) {
                $count++;
                if ($count > 10) {
                    break;
                } // Limit to top 10
                $message .= "§b{$count}. §f{$name} - §a$" . number_format($bankMoney) . "\n";
            }
            $sender->sendMessage($message);
        });

        return true;
    }
}
