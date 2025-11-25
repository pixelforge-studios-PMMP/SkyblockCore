<?php

declare(strict_types=1);

namespace Biswajit\Core\Utils;

use Biswajit\Core\Items\items\guiItems;
use Biswajit\Core\Items\items\menuItem;
use Biswajit\Core\Items\items\minionHeads;
use customiesdevs\customies\item\CustomiesItemFactory;

class ItemLoader
{
    public static function initialize(): void
    {
        $factory = CustomiesItemFactory::getInstance();
        $factory->registerItem(fn () => clone new menuItem(), "skyblock:menu", null);
        $factory->registerItem(fn () => clone new guiItems("close"), "skyblock:close", null);
        $factory->registerItem(fn () => clone new guiItems("collect_all"), "skyblock:collect_all", null);
        $factory->registerItem(fn () => clone new guiItems("upgrade_slot"), "skyblock:upgrade_slot", null);
        $factory->registerItem(fn () => clone new guiItems("glass"), "skyblock:glass", null);
        $factory->registerItem(fn () => clone new guiItems("glass2"), "skyblock:glass2", null);
        $factory->registerItem(fn () => clone new minionHeads("cobblestone_minion", "cobblestone", "Miner"), "skyblock:cobblestone", null);
        $factory->registerItem(fn () => clone new minionHeads("iron_minion", "iron ore", "Miner"), "skyblock:iron_ingot", null);
        $factory->registerItem(fn () => clone new minionHeads("coal_minion", "coal ore", "Miner"), "skyblock:coal", null);
        $factory->registerItem(fn () => clone new minionHeads("gold_minion", "gold ore", "Miner"), "skyblock:gold_ingot", null);
        $factory->registerItem(fn () => clone new minionHeads("lapis_minion", "lapis lazuli ore", "Miner"), "skyblock:lapis_lazuli", null);
        $factory->registerItem(fn () => clone new minionHeads("redstone_minion", "redstone ore", "Miner"), "skyblock:redstone_dust", null);
        $factory->registerItem(fn () => clone new minionHeads("diamond_minion", "diamond ore", "Miner"), "skyblock:diamond", null);
        $factory->registerItem(fn () => clone new minionHeads("emerald_minion", "emerald ore", "Miner"), "skyblock:emerald", null);
        $factory->registerItem(fn () => clone new minionHeads("carrot_minion", "carrot", "Farmer"), "skyblock:carrot", null);
        $factory->registerItem(fn () => clone new minionHeads("potato_minion", "potato", "Farmer"), "skyblock:potato", null);
        $factory->registerItem(fn () => clone new minionHeads("wheat_minion", "wheat", "Farmer"), "skyblock:wheat", null);
        $factory->registerItem(fn () => clone new minionHeads("melon_minion", "melon", "Farmer"), "skyblock:melon", null);
        $factory->registerItem(fn () => clone new minionHeads("pumpkin_minion", "pumpkin", "Farmer"), "skyblock:pumpkin", null);
        $factory->registerItem(fn () => clone new minionHeads("acacia_minion", "acacia log", "Forager"), "skyblock:acacia_log", null);
        $factory->registerItem(fn () => clone new minionHeads("birch_minion", "birch log", "Forager"), "skyblock:birch_log", null);
        $factory->registerItem(fn () => clone new minionHeads("dark_oak_minion", "dark oak log", "Forager"), "skyblock:dark_oak_log", null);
        $factory->registerItem(fn () => clone new minionHeads("jungle_minion", "jungle log", "Forager"), "skyblock:jungle_log", null);
        $factory->registerItem(fn () => clone new minionHeads("oak_minion", "oak log", "Forager"), "skyblock:oak_log", null);
        $factory->registerItem(fn () => clone new minionHeads("spruce_minion", "spruce log", "Forager"), "skyblock:spruce_log", null);
        $factory->registerItem(fn () => clone new minionHeads("cow_minion", "cow", "Slayer"), "skyblock:cow", null);
        $factory->registerItem(fn () => clone new minionHeads("pig_minion", "pig", "Slayer"), "skyblock:pig", null);
        $factory->registerItem(fn () => clone new minionHeads("sheep_minion", "sheep", "Slayer"), "skyblock:sheep", null);
        $factory->registerItem(fn () => clone new minionHeads("chicken_minion", "chicken", "Slayer"), "skyblock:chicken", null);
        $factory->registerItem(fn () => clone new minionHeads("zombie_minion", "zombie", "Slayer"), "skyblock:zombie", null);
        $factory->registerItem(fn () => clone new minionHeads("skeleton_minion", "skeleton", "Slayer"), "skyblock:skeleton", null);
        $factory->registerItem(fn () => clone new minionHeads("spider_minion", "spider", "Slayer"), "skyblock:spider", null);
        $factory->registerItem(fn () => clone new minionHeads("creeper_minion", "creeper", "Slayer"), "skyblock:creeper", null);
    }
}
