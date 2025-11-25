<?php

declare(strict_types=1);

namespace Biswajit\Core\Sessions;

use Biswajit\Core\Skyblock;
use poggit\libasynql\DataConnector;

class IslandData
{
    private string $islandName;
    private array $data;
    private DataConnector $db;
    private static array $cache = [];

    public function __construct(string $islandName, array $initialData = [])
    {
        $this->islandName = $islandName;
        $this->db = Skyblock::getInstance()->getDataBase();
        $this->data = $initialData;
    }

    public function getVisit(): bool
    {
        return $this->data['visit'] ?? false;
    }

    public function setVisit(bool $status): void
    {
        $this->data['visit'] = $status;
        $this->save();
    }

    public function getPartners(): array
    {
        return $this->data['partners'] ?? [];
    }

    public function addPartner(string $partner): void
    {
        $partners = $this->getPartners();
        if (!in_array($partner, $partners)) {
            $partners[] = $partner;
            $this->data['partners'] = $partners;
            $this->save();
        }
    }

    public function removePartner(string $partner): void
    {
        $partners = $this->getPartners();
        if (($key = array_search($partner, $partners)) !== false) {
            unset($partners[$key]);
            $this->data['partners'] = array_values($partners);
            $this->save();
        }
    }

    public function getBanneds(): array
    {
        return $this->data['banneds'] ?? [];
    }

    public function addBanned(string $player): void
    {
        $banneds = $this->getBanneds();
        if (!in_array($player, $banneds)) {
            $banneds[] = $player;
            $this->data['banneds'] = $banneds;
            $this->save();
        }
    }

    public function removeBanned(string $player): void
    {
        $banneds = $this->getBanneds();
        if (($key = array_search($player, $banneds)) !== false) {
            unset($banneds[$key]);
            $this->data['banneds'] = array_values($banneds);
            $this->save();
        }
    }

    public function getSettings(): array
    {
        return $this->data['settings'] ?? [];
    }

    public function setSettings(array $settings): void
    {
        $this->data['settings'] = $settings;
        $this->save();
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function save(): void
    {
        $jsonData = json_encode($this->data);
        $this->db->executeChange("skyblockIsland.saveIslandData", [
            "player" => $this->islandName,
            "data" => $jsonData
        ]);
    }

    public function delete(): void
    {
        unset(self::$cache[$this->islandName]);
        $this->db->executeGeneric("skyblockIsland.deleteIslandData", [
            "player" => $this->islandName
        ]);

        $this->data = [];
    }

    public static function get(string $islandName, callable $callback): void
    {
        if (isset(self::$cache[$islandName])) {
            $callback(self::$cache[$islandName]);
            return;
        }

        Skyblock::getInstance()->getDataBase()->executeSelect("skyblockIsland.getIslandData", ["player" => $islandName], function (array $rows) use ($callback, $islandName): void {
            if (count($rows) > 0) {
                $data = new self($islandName, json_decode($rows[0]["data"], true));
                self::$cache[$islandName] = $data;
                $callback($data);
            } else {
                $callback(null);
            }
        });
    }

    public static function getSync(string $islandName): ?IslandData
    {
        return self::$cache[$islandName] ?? null;
    }

    public static function create(string $islandName, array $initialData = []): IslandData
    {
        $session = new self($islandName, $initialData);
        self::$cache[$islandName] = $session;
        $session->save();
        return $session;
    }
}
