<?php

declare(strict_types=1);

namespace Biswajit\Core;

use Biswajit\Core\Entitys\Minion\MinionEntity;
use Biswajit\Core\Managers\IslandManager;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\math\AxisAlignedBB;
use pocketmine\resourcepacks\ZippedResourcePack;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\World;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Filesystem\Path;
use ZipArchive;

class API
{
    public static array $vanish = [];

    /**
     * Loads or creates the hub world if it doesn't exist
     */
    public static function loadHub(): void
    {
        $world = Skyblock::getInstance()->getConfig()->get("HUB");
        $worldPath = Skyblock::getInstance()->getServer()->getDataPath() . "worlds/" . $world;

        if (!file_exists($worldPath)) {
            self::createHub();
            Skyblock::getInstance()->getLogger()->info("§aHub world '$world' has been created successfully");
        }
    }

    /**
     * Loads the hub world if it exists
     */
    public static function loadHubWorld(): void
    {
        $world = Skyblock::getInstance()->getConfig()->get("HUB");
        $worldPath = Skyblock::getInstance()->getServer()->getDataPath() . "worlds/" . $world;

        if (file_exists($worldPath)) {
            if (Skyblock::getInstance()->getServer()->getWorldManager()->loadWorld($world)) {
                Skyblock::getInstance()->getLogger()->info("§eHub world loaded successfully");
            } else {
                Skyblock::getInstance()->getLogger()->error("§cFailed to load hub world '$world'");
            }
            return;
        }

        self::createHub();
        IslandManager::loadIslands();
    }

    /**
     * Creates the hub world from zip file
     */
    public static function createHub(): void
    {
        $worldPath = Skyblock::getInstance()->getDataFolder() . "HUB.zip";
        $hubName = self::getHub();
        $targetPath = Server::getInstance()->getDataPath() . "worlds/" . $hubName;

        if (!file_exists($worldPath)) {
            Skyblock::getInstance()->getLogger()->error("§cHub world template not found!");
            return;
        }

        if (!is_dir($targetPath)) {
            $zip = new ZipArchive();
            if ($zip->open($worldPath) === true) {
                mkdir($targetPath);
                $zip->extractTo($targetPath);
                $zip->close();
                Server::getInstance()->getWorldManager()->loadWorld($hubName);
            } else {
                Skyblock::getInstance()->getLogger()->error("§cFailed to extract hub world!");
            }
        }
    }

    /**
     * Loads minion skin resources
     */
    public static function loadMinionSkins(): void
    {
        $path = Skyblock::getInstance()->getDataFolder() . "minion/minion.zip";
        if (!file_exists($path)) {
            Skyblock::getInstance()->getLogger()->error("§cMinion skins file not found!");
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($path) === true) {
            $zip->extractTo(Skyblock::getInstance()->getDataFolder() . "minion");
            $zip->close();
        }
    }

	/**
	 * Applies a resource pack to the server
	 * @throws ReflectionException
	 */
    public static function applyResourcePack(): void
    {
        $path = Skyblock::getInstance()->getDataFolder() . "skyblockPack.zip";
        if (!file_exists($path)) {
            return;
        }

        $rpManager = Skyblock::getInstance()->getServer()->getResourcePackManager();

        foreach ($rpManager->getResourceStack() as $pack) {
            if ($pack instanceof ZippedResourcePack && $pack->getPath() === Path::join(Skyblock::getInstance()->getDataFolder(), "skyblockPack.zip")) {
                return;
            }
        }

        $rpManager->setResourceStack(array_merge($rpManager->getResourceStack(), [new ZippedResourcePack(Path::join(Skyblock::getInstance()->getDataFolder(), "skyblockPack.zip"))]));
        (new ReflectionProperty($rpManager, "serverForceResources"))->setValue($rpManager, true);
    }

    /**
     * Gets the hub world name from config
     */
    public static function getHub(): string
    {
        return Skyblock::getInstance()->getConfig()->get("HUB");
    }

    public static function getEntities(World $world, $class): array
    {
        $array = array();
        $entities = $world->getEntities();
        foreach ($entities as $entity) {
            if ($entity instanceof $class) {
                $array[] = $entity;
            }
        }
        return $array;
    }

    public static function getMinions(World $world): array
    {
        $array = array();
        $entities = $world->getEntities();
        foreach ($entities as $entity) {
            if ($entity instanceof MinionEntity) {
                $array[] = $entity;
            }
        }
        return $array;
    }

    /**
     * Sets and freezes hub world time
     */
    public static function setHubTime(): void
    {
        $world = Server::getInstance()->getWorldManager()->getWorldByName(self::getHub());
        if ($world !== null) {
            $world->setTime(1000);
            $world->stopTime();
        }
    }

    public static function getSkinPath(string $vanillaName): string
    {
        return match ($vanillaName) {
            "cobblestone" => "minion/cobblestone.png",
            "emerald ore" => "minion/emerald.png",
            "diamond ore" => "minion/diamond.png",
            "gold ore" => "minion/gold.png",
            "iron ore" => "minion/iron.png",
            "coal ore" => "minion/coal.png",
            "lapis lazuli ore" => "minion/lapis.png",
            "redstone ore" => "minion/redstone.png",
            "carrot" => "minion/carrot.png",
            "potato" => "minion/potato.png",
            "wheat seeds" => "minion/wheat.png",
            "melon" => "minion/melon.png",
            "pumpkin" => "minion/pumpkin.png",
            "acacia log" => "minion/acacia.png",
            "birch log" => "minion/birch.png",
            "dark oak log" => "minion/dark_oak.png",
            "jungle log" => "minion/jungle.png",
            "oak log" => "minion/oak.png",
            "spruce log" => "minion/spruce.png",
            "cow" => "minion/cow.png",
            "pig" => "minion/pig.png",
            "sheep" => "minion/sheep.png",
            "chicken" => "minion/chicken.png",
            "zombie" => "minion/zombie.png",
            "skeleton" => "minion/skeleton.png",
            "spider" => "minion/spider.png",
            "creeper" => "minion/creeper.png",
            default => "minion/minion.png"
        };
    }

    public static function getPlayerWorld(Player $player): ?string
    {
        $worldName = $player->getWorld()->getFolderName();

        if ($worldName === self::getHub()) {
            $mineRegion = new AxisAlignedBB(13.00, (float) World::Y_MIN, 112.00, 255.00, (float) World::Y_MAX, 461.00);
            $forestRegion = new AxisAlignedBB(-231.00, (float) World::Y_MIN, -554.00, 26.00, (float) World::Y_MAX, -120.00);
            $FarmingRegion = new AxisAlignedBB(-256.00, World::Y_MIN, -95.00, -42.00, World::Y_MAX, 52.00);
            $gravyaRegion = new AxisAlignedBB(89.00, World::Y_MIN, -94.00, 449.00, World::Y_MAX, 83.00);

            $position = $player->getPosition();
            if ($mineRegion->isVectorInXZ($position)) {
                return "§a Mine";
            } elseif ($forestRegion->isVectorInXZ($position)) {
                return "§a Forest";
            } elseif ($FarmingRegion->isVectorInXZ($position)) {
                return "§a Farming";
            } elseif ($gravyaRegion->isVectorInXZ($position)) {
                return "§a Graveyard";
            } else {
                return "§a Village";
            }
        }

        if ($worldName === $player->getName()) {
            return "§a Your Island";
        }

        return "§a Guest Island";
    }

    /**
     * Gets a message from messages.yml
     */
    public static function getMessage(string $key, array $replace = []): string|array
    {
        $file = new Config(Skyblock::getInstance()->getDataFolder() . "messages.yml", Config::YAML, []);
        $message = $file->getNested($key) ?? "Message '$key' not found";
        foreach ($replace as $search => $value) {
            $message = str_replace($search, $value, $message);
        }
        return $message;
    }

    /**
     * Gets a custom Skyblock item by identifier
     */
    public static function getItem(string $identifier): Item
    {
        return StringToItemParser::getInstance()->parse("skyblock:$identifier");
    }
}
