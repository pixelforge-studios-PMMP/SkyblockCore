<?php

declare(strict_types=1);

namespace Biswajit\Core\Utils;

use Biswajit\Core\Commands\player\BankCommand;
use Biswajit\Core\Commands\player\EmojisCommand;
use Biswajit\Core\Commands\player\FlyCommand;
use Biswajit\Core\Commands\player\HubCommand;
use Biswajit\Core\Commands\player\IslandCommand;
use Biswajit\Core\Commands\player\JoinCommand;
use Biswajit\Core\Commands\player\TopBankCommand;
use Biswajit\Core\Commands\player\TopMoneyCommand;
use Biswajit\Core\Commands\player\VisitCommand;
use Biswajit\Core\Commands\player\WeatherCommand;
use Biswajit\Core\Commands\Staff\CreateRecipeCommand;
use Biswajit\Core\Commands\Staff\EconomyCommand;
use Biswajit\Core\Commands\Staff\GemsCommand;
use Biswajit\Core\Commands\Staff\MultiWorld;
use Biswajit\Core\Commands\Staff\RankCommand;
use Biswajit\Core\Commands\Staff\SetEntityCommand;
use Biswajit\Core\Entitys\Minion\types\FarmerMinion;
use Biswajit\Core\Entitys\Minion\types\ForagingMinion;
use Biswajit\Core\Entitys\Minion\types\MinerMinion;
use Biswajit\Core\Entitys\Minion\types\SlayerMinion;
use Biswajit\Core\Entitys\Vanilla\Chicken;
use Biswajit\Core\Entitys\Vanilla\Cow;
use Biswajit\Core\Entitys\Vanilla\Creeper;
use Biswajit\Core\Entitys\Vanilla\Pig;
use Biswajit\Core\Entitys\Vanilla\Sheep;
use Biswajit\Core\Entitys\Vanilla\Skeleton;
use Biswajit\Core\Entitys\Vanilla\Spider;
use Biswajit\Core\Entitys\Vanilla\Zombie;
use Biswajit\Core\Listeners\Entity\EntityAttackEvent;
use Biswajit\Core\Listeners\Entity\EntityDamageByEntity;
use Biswajit\Core\Listeners\Entity\EntityRegainHealth;
use Biswajit\Core\Listeners\Entity\EntityTeleport;
use Biswajit\Core\Listeners\Entity\EntityTrampleFarmland;
use Biswajit\Core\Listeners\Inventory\InventoryTransaction;
use Biswajit\Core\Listeners\Player\PlayerChat;
use Biswajit\Core\Listeners\Player\PlayerInteract;
use Biswajit\Core\Listeners\Server\IslandListener;
use Biswajit\Core\Listeners\Player\PlayerCreation;
use Biswajit\Core\Listeners\Player\PlayerExhaust;
use Biswajit\Core\Listeners\Player\PlayerJoin;
use Biswajit\Core\Listeners\Player\PlayerQuit;
use Biswajit\Core\Listeners\Server\HubListener;
use Biswajit\Core\Listeners\Server\QueryRegenerate;
use Biswajit\Core\Skyblock;
use Biswajit\Core\Tasks\ActionbarTask;
use Biswajit\Core\Tasks\BroadcastTask;
use Biswajit\Core\Tasks\ClearLagTask;
use Biswajit\Core\Tasks\EntitySpawnerTask;
use Biswajit\Core\Tasks\LoanTask;
use Biswajit\Core\Tasks\RankTask;
use Biswajit\Core\Tasks\ScoreBoardTask;
use Biswajit\Core\Tasks\StatsRegainTask;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class Loader
{
    public static function initialize(): void
    {
        self::loadListeners();
        self::loadCommands();
        self::loadEntitys();
        self::loadTasks();
        ItemLoader::initialize();
        BlockLoader::initialize();
    }

    public static function loadListeners(): void
    {
        $listeners = [
             new PlayerJoin(),
             new PlayerQuit(),
             new PlayerCreation(),
             new IslandListener(),
             new InventoryTransaction(),
             new HubListener(),
             new EntityTrampleFarmland(),
             new EntityDamageByEntity(),
             new EntityRegainHealth(),
             new PlayerExhaust(),
             new QueryRegenerate(),
             new PlayerInteract(),
             new EntityAttackEvent(),
             new PlayerChat(),
             new EntityTeleport()
        ];

        foreach ($listeners as $event) {
            Skyblock::getInstance()->getServer()->getPluginManager()->registerEvents($event, Skyblock::getInstance());
        }

        $count = count($listeners);
        Skyblock::getInstance()->getLogger()->info("§c{$count}§f Listeners register !");
    }

    public static function loadCommands(): void
    {
        $commands = [
            new IslandCommand(),
            new WeatherCommand(),
            new JoinCommand(),
            new VisitCommand(),
            new HubCommand(),
            new MultiWorld(),
            new EconomyCommand(),
            new BankCommand(),
            new TopBankCommand(),
            new TopMoneyCommand(),
            new SetEntityCommand(),
            new GemsCommand(),
            new RankCommand(),
            new CreateRecipeCommand(),
            new FlyCommand(),
            new EmojisCommand()
        ];

        foreach ($commands as $cmd) {
            Skyblock::getInstance()->getServer()->getCommandMap()->register("skyblock", $cmd);
        }

        $count = count($commands);
        Skyblock::getInstance()->getLogger()->info("§c{$count}§f command register !");
    }

    public static function loadEntitys(): void
    {

        EntityFactory::getInstance()->register(MinerMinion::class, function (World $world, CompoundTag $nbt): MinerMinion {
            return new MinerMinion(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["entity:MinerMinion", 'MinerMinion']);

        EntityFactory::getInstance()->register(FarmerMinion::class, function (World $world, CompoundTag $nbt): FarmerMinion {
            return new FarmerMinion(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["entity:FarmerMinion", 'FarmerMinion']);

        EntityFactory::getInstance()->register(ForagingMinion::class, function (World $world, CompoundTag $nbt): ForagingMinion {
            return new ForagingMinion(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["entity:ForagingMinion", 'ForagingMinion']);

        EntityFactory::getInstance()->register(SlayerMinion::class, function (World $world, CompoundTag $nbt): SlayerMinion {
            return new SlayerMinion(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["entity:SlayerMinion", 'SlayerMinion']);

        EntityFactory::getInstance()->register(Zombie::class, function (World $world, CompoundTag $nbt): Zombie {
            return new Zombie(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Zombie", 'Zombie']);

        EntityFactory::getInstance()->register(Skeleton::class, function (World $world, CompoundTag $nbt): Skeleton {
            return new Skeleton(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Skeleton", 'Skeleton']);

        EntityFactory::getInstance()->register(Creeper::class, function (World $world, CompoundTag $nbt): Creeper {
            return new Creeper(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Creeper", 'Creeper']);

        EntityFactory::getInstance()->register(Spider::class, function (World $world, CompoundTag $nbt): Spider {
            return new Spider(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Spider", 'Spider']);

        EntityFactory::getInstance()->register(Pig::class, function (World $world, CompoundTag $nbt): Pig {
            return new Pig(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Pig", 'Pig']);

        EntityFactory::getInstance()->register(Cow::class, function (World $world, CompoundTag $nbt): Cow {
            return new Cow(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Cow", 'Cow']);

        EntityFactory::getInstance()->register(Sheep::class, function (World $world, CompoundTag $nbt): Sheep {
            return new Sheep(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Sheep", 'Sheep']);

        EntityFactory::getInstance()->register(Chicken::class, function (World $world, CompoundTag $nbt): Chicken {
            return new Chicken(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["entity:Chicken", 'Chicken']);

    }

    public static function loadTasks(): void
    {
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new EntitySpawnerTask(), 20 * 60);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ActionbarTask(), 10);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new StatsRegainTask(), 100);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new LoanTask(Skyblock::getInstance()), 100);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ClearLagTask(), 20);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreBoardTask(), 20);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new RankTask(Skyblock::getInstance()), 20);
        Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new BroadcastTask(Skyblock::getInstance()), 1200);
    }

}
