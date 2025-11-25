<?php

declare(strict_types=1);

namespace Biswajit\Core\Tasks;

use Biswajit\Core\Managers\BankManager;
use Biswajit\Core\Skyblock;
use pocketmine\scheduler\Task;

class LoanTask extends Task
{
    /** @var Skyblock */
    private Skyblock $source;

    public function __construct(Skyblock $source)
    {
        $this->source = $source;
    }

    public function onRun(): void
    {
        $this->checkLoans();
    }

    public function checkLoans(): void
    {
        foreach ($this->source->getServer()->getOnlinePlayers() as $player) {
            if (BankManager::getLoan($player) > 0) {
                if (BankManager::getLoanTime($player) < time()) {
                    BankManager::recoverLoan($player);
                }
            }
        }
    }
}
