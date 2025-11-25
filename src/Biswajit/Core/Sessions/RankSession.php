<?php


declare(strict_types=1);

namespace Biswajit\Core\Sessions;

use Biswajit\Core\Skyblock;

trait RankSession
{
    private array $rankData = [];

    public function loadRank(): void
    {
        Skyblock::getInstance()->getDataBase()->executeSelect(
            'rank.load',
            [
                'uuid' => $this->getUniqueId()->toString()
            ],
            function (array $rows): void {
                if (count($rows) == 0) {
                    $this->createRank();
                    return;
                }
                $this->rankData = json_decode($rows[0]['data'], true);
            }
        );
    }

    public function createRank(): void
    {
        $this->setRank("Rank", "Default");
        $this->setRank("expiry", "Never");
        Skyblock::getInstance()->getDataBase()->executeInsert(
            'rank.create',
            [
                 'uuid' => $this->getUniqueId()->toString(),
                 'data' => json_encode([])
             ]
        );
    }

    public function saveRank(): void
    {
        if (empty($this->rankData)) {
            return;
        }

        Skyblock::getInstance()->getDataBase()->executeChange('rank.update', [
            'uuid' => $this->getUniqueId()->toString(),
            'data' => json_encode($this->rankData)
        ]);
        Skyblock::getInstance()->getDataBase()->waitAll();
        unset($this->rankData);
    }

    public function setRank(string $key, mixed $value): void
    {
        $this->rankData[$key] = $value;
    }

    public function getRank(string $key): mixed
    {
        return $this->rankData[$key] ?? null;
    }
}
