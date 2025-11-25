<?php

declare(strict_types=1);

namespace Biswajit\Core\Sessions;

use Biswajit\Core\Skyblock;

trait AreaSession
{
    private array $areaData = [];

    public function loadArea(): void
    {
        Skyblock::getInstance()->getDataBase()->executeSelect(
            'area.load',
            [
                'uuid' => $this->getUniqueId()->toString()
            ],
            function (array $rows): void {
                if (count($rows) === 0) {
                    $this->createArea();
                    return;
                }
                $this->areaData = json_decode($rows[0]['data'], true, 512, JSON_THROW_ON_ERROR);
            }
        );
    }

    public function createArea(): void
    {
        Skyblock::getInstance()->getDataBase()->executeInsert(
            'area.create',
            [
                 'uuid' => $this->getUniqueId()->toString(),
                 'data' => json_encode([])
             ]
        );
    }

    public function saveArea(): void
    {
        if (empty($this->areaData)) {
            return;
        }

        Skyblock::getInstance()->getDataBase()->executeChange('area.update', [
            'uuid' => $this->getUniqueId()->toString(),
            'data' => json_encode($this->areaData)
        ]);
        Skyblock::getInstance()->getDataBase()->waitAll();
        unset($this->areaData);
    }

    public function addArea(string $key, string $value): void
    {
        $areas = $this->getArea($key);
        $areas[] = $value;
        $this->areaData[$key] = $areas;
    }

    public function getArea(string $key): array
    {
        return $this->areaData[$key] ?? [];
    }
}
