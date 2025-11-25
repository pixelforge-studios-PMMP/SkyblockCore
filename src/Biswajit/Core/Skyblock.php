<?php

declare(strict_types=1);

namespace Biswajit\Core;

use Biswajit\Core\Managers\BlockManager;
use Biswajit\Core\Managers\CoreManager;
use Biswajit\Core\Managers\ScoreBoardManager;
use Biswajit\Core\Managers\Worlds\IslandGenerator;
use Biswajit\Core\Tasks\AsynTasks\loadDataTask;
use Biswajit\Core\Utils\CraftingTableInvMenuType;
use Biswajit\Core\Utils\Loader;
use Biswajit\Core\Utils\Utils;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\generator\GeneratorManager;

class Skyblock extends PluginBase
{
    use SingletonTrait;
    use Database;

    public static string $prefix;
    public const FAKE_ENCH_ID = 500;
    private array $handlers = [];

    public static array $profanities = [];

    //thanks to muqsit for portable crafting
    public const INV_MENU_TYPE_WORKBENCH = "portablecrafting:workbench";


    public function onLoad(): void
    {
        self::$instance = $this;
        self::$prefix = $this->getConfig()->get("PREFIX");
        ScoreBoardManager::setScoreboard($this->getConfig()->get("SCOREBOARD-TITLE"));

        $this->reloadConfig();

        $worlds = [
            "hub" => [
                "url" => "https://github.com/pixelforge-studios-PMMP/SkyblockCoreWorlds/releases/download/Worlds/HUB.zip",
                "path" => $this->getDataFolder() . "HUB.zip",
                "type" => "hub"
            ],
            "islands" => [
                "url" => "https://github.com/pixelforge-studios-PMMP/SkyblockCoreWorlds/releases/download/Worlds/Islands.zip",
                "path" => $this->getDataFolder() . "island" . DIRECTORY_SEPARATOR . "Islands.zip",
                "type" => "island"
            ],
            "pack" => [
                "url" => "https://github.com/pixelforge-studios-PMMP/SkyblockCorePack/releases/download/pack/skyblockPack.zip",
                "path" => $this->getDataFolder() . "skyblockPack.zip",
                "type" => "pack"
            ]
        ];

        foreach ($worlds as $name => $data) {
            if (!file_exists($data["path"])) {
                try {
                    $this->getLogger()->info("Downloading " . $name . " world...");
                    $this->getServer()->getAsyncPool()->submitTask(new loadDataTask($data["url"], $data["path"], $data["type"]));
                } catch (\Exception $e) {
                    $this->getLogger()->error("Failed to download " . $name . " world: " . $e->getMessage());
                }
            }
        }

        GeneratorManager::getInstance()->addGenerator(IslandGenerator::class, "void", fn () => null, true);
        EnchantmentIdMap::getInstance()->register(self::FAKE_ENCH_ID, new Enchantment("Glow", 1, 0xffff, 0x0, 1));

        @mkdir($this->getDataFolder() . "island");
        @mkdir($this->getDataFolder() . "minion");
        @mkdir($this->getDataFolder() . "recipes");

        $this->saveResource("minion/minion.zip");
        $this->saveResource("minion/minion.geo.json");
        $this->saveResource("messages.yml");
        $this->saveResource("entity.yml");
        $this->saveResource("Skyblock.mcpack");
        $this->saveResource("ranks.yml");
        $this->saveResource("emojis.yml");
        $this->saveResource("profanity_filter.wlist");

        $this->getLogger()->info("§l§bLoading SkyblockCore Version: ". TextFormat::YELLOW . Utils::getVersion());

        $profanities = file($this->getDataFolder() . "profanity_filter.wlist", FILE_IGNORE_NEW_LINES);
        self::$profanities = !$profanities ? [] : $profanities;

    }

    public function onEnable(): void
    {
        
        $this->getServer()->getNetwork()->setName($this->getConfig()->get("SERVER-MOTD"));

        BlockManager::initialise();

        $this->initDatabase();

        API::loadMinionSkins();
        API::loadHubWorld();
        API::setHubTime();
        API::applyResourcePack();

        CoreManager::initialise();
        CoreManager::getInstance();

        Loader::initialize();

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        InvMenuHandler::getTypeRegistry()->register(self::INV_MENU_TYPE_WORKBENCH, new CraftingTableInvMenuType());
    }

    public function onDisable(): void
    {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            if (!$player instanceof Player) return;
            $player->sendTitle("§cServer Restarting");
            $player->saveAll();
        }

        CoreManager::getInstance()->sendShutdown();
        BlockManager::Disable();
    }

    public function addHandler($handler): void
    {
        $this->handlers[] = $handler;
    }

    public function getRecipeFile($recipe): Config
    {
        $this->saveResource("recipes/$recipe.yml");
		return new Config($this->getDataFolder() . "recipes/$recipe.yml", Config::YAML, []);
    }

    public function getEmojis(): Config
    {
        return new Config($this->getDataFolder() . "emojis.yml", Config::YAML, []);
    }
}
