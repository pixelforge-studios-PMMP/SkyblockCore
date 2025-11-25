<?php


declare(strict_types=1);

namespace Biswajit\Core\Sessions;

use Biswajit\Core\Skyblock;

trait SessionsData
{
    public static string $weathers;
    private array $data = [];

    public function loadData(): void
    {
        Skyblock::getInstance()->getDataBase()->executeSelect(
            'skyblock.load',
            [
                'uuid' => $this->getUniqueId()->toString()
            ],
            function (array $rows): void {
                if (count($rows) == 0) {
                    $this->createData();
                    return;
                }
                $this->data = json_decode($rows[0]['data'], true);
            }
        );
    }

    public function createData(): void
    {
        Skyblock::getInstance()->getDataBase()->executeInsert(
            'skyblock.create',
            [
                 'uuid' => $this->getUniqueId()->toString(),
                 'data' => json_encode([])
             ]
        );
        $this->data = [];
    }

    public function saveData(): void
    {
        if (empty($this->data)) {
            return;
        }

        Skyblock::getInstance()->getDataBase()->executeChange('skyblock.update', [
            'uuid' => $this->getUniqueId()->toString(),
            'data' => json_encode($this->data)
        ]);
        Skyblock::getInstance()->getDataBase()->waitAll();
        unset($this->data);
    }

    public function getHealth(): float
    {
        return $this->data["Health"] ?? 100;
    }

    public function getMaxHealth(): int
    {
        return $this->data["MaxHealth"] ?? 100;
    }

    public function getDefense(): int
    {
        return $this->data["Defense"] ?? 0;
    }

    public function getMana(): float
    {
        return $this->data["Mana"] ?? 100;
    }

    public function getMaxMana(): int
    {
        return $this->data["MaxMana"] ?? 100;
    }

    public function setHealth(float $amount): void
    {
        $this->data["Health"] = $amount;
    }

    public function setMaxHealth(int $amount): void
    {
        $this->data["MaxHealth"] = $amount;
    }

    public function setDefense(int $amount): void
    {
        $this->data["Defense"] = $amount;
    }

    public function setMana(float $amount): void
    {
        $this->data["Mana"] = $amount;
    }

    public function setMaxMana(int $amount): void
    {
        $this->data["MaxMana"] = $amount;
    }

    public function getVision(): bool
    {
        return $this->data["Vision"] ?? false;
    }

    public function setVision(bool $value): void
    {
        $this->data["Vision"] = $value;
    }

    public function getSpeed(): bool
    {
        return $this->data["Speed"] ?? false;
    }

    public function setSpeed(bool $value): void
    {
        $this->data["Speed"] = $value;
    }

}
