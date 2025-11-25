<?php

declare(strict_types=1);

namespace Biswajit\Core;

use Biswajit\Core\Managers\EconomyManager;
use Biswajit\Core\Sessions\AreaSession;
use Biswajit\Core\Sessions\EconomySession;
use Biswajit\Core\Sessions\RankSession;
use Biswajit\Core\Sessions\SessionsData;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player as PMMPPlayer;

class Player extends PMMPPlayer
{
    use SessionsData;
    use EconomySession;
    use RankSession;
    use AreaSession;

    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->loadAll();
    }

    public function damagePlayer(float $amount): void
    {

        $this->setHealth($this->getHealth() - $amount);
        $this->doHitAnimation();

        if ($this->getHealth() <= 0) {
            $world = $this->getWorld();
            $this->teleport($world->getSpawnLocation());
            $this->setHealth($this->getMaxHealth());
            $amount =  (50.0 / 100) * EconomyManager::getMoney($this);
            EconomyManager::subtractMoney($this, $amount);
            $this->sendMessage(Skyblock::$prefix . API::getMessage("player-death", ["{amount}" => (string)$amount]));
        }
    }

    public function loadAll(): void
    {
        $this->loadData();
        $this->loadEconomy();
        $this->loadRank();
        $this->loadArea();
    }

    public function saveAll(): void
    {
        $this->saveData();
        $this->saveEconomy();
        $this->saveRank();
        $this->saveArea();
    }
}
