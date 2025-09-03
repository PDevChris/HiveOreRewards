<?php

namespace HiveOreRewards;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\StringToItemParser;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\event\player\PlayerDeathEvent;

class Main extends PluginBase implements Listener {

    protected \pocketmine\utils\Config $config;

    /** Default supported ores */
    private array $defaultOres = [
        "diamond_ore",
        "emerald_ore",
        "lapis_ore",
        "gold_ore",
        "iron_ore",
        "coal_ore",
        "redstone_ore"
    ];

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = $this->getConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void {
    $player = $event->getPlayer();
    $player->setMaxHealth(20); // Reset max health to default
}

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        // Toggle vanilla drops and XP
        if(!$this->config->get("vanilla_drops", false)){
            $event->setDrops([]);
        }
        if(!$this->config->get("vanilla_xp", false)){
            $event->setXpDropAmount(0);
        }

        $oreConfig = $this->config->get("ores", []);

        foreach($this->defaultOres as $oreName){
            $enabled = $oreConfig[$oreName]["enabled"] ?? false;
            if(!$enabled) continue;

            $oreBlock = StringToItemParser::getInstance()->parse($oreName)?->getBlock();
            if($oreBlock !== null && $block->getTypeId() === $oreBlock->getTypeId()){
                if($oreName === "redstone_ore"){ 
                    $this->giveRedstoneHeart($player);
                    $this->playSound($player, "block.end_portal_frame.fill");
                } else {
                    $settings = $oreConfig[$oreName];
                    $this->giveConfiguredDrop($player, $oreName, $settings);
                    $this->applyEffects($player, $settings);
                }
                break;
            }
        }

        // Check optional ores from config
        foreach($oreConfig as $oreName => $settings){
            if(in_array($oreName, $this->defaultOres)) continue; // skip default, already handled
            if(!($settings["enabled"] ?? false)) continue;

            $oreBlock = StringToItemParser::getInstance()->parse($oreName)?->getBlock();
            if($oreBlock !== null && $block->getTypeId() === $oreBlock->getTypeId()){
                $this->giveConfiguredDrop($player, $oreName, $settings);
                $this->applyEffects($player, $settings);
                break;
            }
        }
    }

    private function playSound(Player $player, string $soundName) {
		$playerPos = $player->getPosition();
		$player->getNetworkSession()->sendDataPacket(
			PlaySoundPacket::create(
				soundName: $soundName,
				x: $playerPos->getX(),
				y: $playerPos->getY(),
				z: $playerPos->getZ(),
				volume: 1.0,
				pitch: 1.0
			)
		);
	}

    /** Give one golden heart per redstone block, max limited by config */
    private function giveRedstoneHeart(Player $player): void {
        $max = $this->config->get("redstone")["max_hearts"] ?? 40;
        $currentHealth = $player->getMaxHealth();

        if($currentHealth < $max){
            $player->setMaxHealth($currentHealth + 2);
            $player->sendMessage("§c❤ You gained an extra heart!");
        } else {
            $player->sendMessage("§7(Max hearts reached!)");
        }
    }

    /** Give drops based on ore settings in config */
   /** Give drops based on ore settings in config (items only) */
    private function giveConfiguredDrop(Player $player, string $oreName, array $settings): void {
    $items = $settings["drops"] ?? [];
    $mode = $settings["drop-mode"] ?? "all";

    // Random drop mode
    if($mode === "random" && !empty($items)){
        $items = [ $items[array_rand($items)] ];
    }

    foreach($items as $itemData){
        // Parse count: e.g., "iron_sword:1"
        $parts = explode(":", $itemData);
        $itemName = $parts[0];
        $count = (int)($parts[1] ?? 1);

        $item = StringToItemParser::getInstance()->parse($itemName);
        if($item === null) {
            $this->getLogger()->warning("Invalid item name '{$itemName}' in ore '{$oreName}'");
            continue;
        }
        $item->setCount($count);

        // Enchantments
        $enchantSection = $this->config->get("enchantments")[$oreName]["items"][$itemName] ?? null;
        if($enchantSection !== null){
            $chance = $enchantSection["chance"] ?? 100;
            if(mt_rand(1,100) <= $chance){
                $maxLevel = $enchantSection["max_level"] ?? 1;
                $possible = $enchantSection["enchantments"] ?? [];

                if(!empty($possible)){
                    $chosen = $possible[array_rand($possible)];
                    $enchant = StringToEnchantmentParser::getInstance()->parse($chosen);

                    if($enchant !== null){
                        $level = mt_rand(1, $maxLevel);
                        $item->addEnchantment(new EnchantmentInstance($enchant, $level));
                    }
                }
            }
        }

        $player->getInventory()->addItem($item);
    }
}



    /** Apply potion effects from config */
    private function applyEffects(Player $player, array $settings): void {
    $effects = $settings["effects"] ?? [];
    if(empty($effects)) return;

    $mode = $settings["effects-mode"] ?? "all"; // "all" or "random"

    if($mode === "random"){
        // Pick one random effect
        $effectData = $effects[array_rand($effects)];
        $typeName = $effectData["type"] ?? "";
        $duration = $effectData["duration"] ?? 100;
        $amplifier = $effectData["amplifier"] ?? 0;

        $effect = StringToEffectParser::getInstance()->parse($typeName);
        if($effect !== null){
            $player->getEffects()->add(new EffectInstance($effect, $duration, $amplifier));
        }
    } else {
        // Apply all effects
        foreach($effects as $effectData){
            $typeName = $effectData["type"] ?? "";
            $duration = $effectData["duration"] ?? 100;
            $amplifier = $effectData["amplifier"] ?? 0;

            $effect = StringToEffectParser::getInstance()->parse($typeName);
            if($effect !== null){
                $player->getEffects()->add(new EffectInstance($effect, $duration, $amplifier));
            }
        }
    }
}

}
