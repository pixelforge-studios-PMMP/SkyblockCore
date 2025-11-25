<?php


declare(strict_types=1);

namespace Biswajit\Core\Sessions;

use Biswajit\Core\Skyblock;

trait EconomySession
{
    private array $economyData = [];

    public function loadEconomy(): void
    {
        Skyblock::getInstance()->getDataBase()->executeSelect(
            'economy.load',
            [
                'uuid' => $this->getUniqueId()->toString()
            ],
            function (array $rows): void {
                if (count($rows) == 0) {
                    $this->createEconomy();
                    return;
                }
                $this->economyData = json_decode($rows[0]['data'], true);
            }
        );
    }

    public function createEconomy(): void
    {
        $this->setEconomy("name", $this->getName());
        Skyblock::getInstance()->getDataBase()->executeInsert(
            'economy.create',
            [
                 'uuid' => $this->getUniqueId()->toString(),
                 'data' => json_encode([$this->economyData])
             ]
        );
    }

    public function saveEconomy(): void
    {
        if (empty($this->economyData)) {
            return;
        }

        Skyblock::getInstance()->getDataBase()->executeChange('economy.update', [
            'uuid' => $this->getUniqueId()->toString(),
            'data' => json_encode($this->economyData)
        ]);
        Skyblock::getInstance()->getDataBase()->waitAll();
        unset($this->economyData);
    }

    public function setEconomy(string $key, mixed $value): void
    {
        $this->economyData[$key] = $value;
    }

    public function getEconomy(string $key): mixed
    {
        return $this->economyData[$key] ?? null;
    }
}
