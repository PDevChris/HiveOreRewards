# HiveOreRewards

Enhance your Minecraft server with custom ore rewards, random drops, enchantments, effects, and a heart system! This is useful for FFA Servers. Break ores to gain items, potion effects, or extra hearts all configurable through a simple YAML file. This is similar's to “Hive” mechanics in SkyWars. 

![1750733594209](https://github.com/user-attachments/assets/f48f90ff-785c-4f3c-9287-b4532548099e)

### Features 

* [X] Custom drops for ores: Players can get items or blocks when breaking ores.
* [X] Randomized drops: Choose whether all items drop or just a random one.
* [X] Enchantments support: Give enchanted items with configurable chance and level.
* [X] Potion effects: Apply effects to players when they break an ore (random or all).
* [X] Extra hearts system: Redstone ore gives extra health, capped by config (This is still being worked on).
* [X] Supports default ores: Diamond, Emerald, Lapis, Gold, Iron, Coal, Redstone.
* [X] Add custom ores and blocks: Just add them in the config—no code changes required!
* [X] IMPORTANT: Reset hearts on death: Player’s max health resets to 20 on death.

### Config Structure
```yaml
# HiveOreRewards Configuration
# PMDevChris

# This affects the regular behavior of minecraft items!
vanilla_drops: false
vanilla_xp: false

redstone:
  max_hearts: 30 # Maximum extra hearts a player can gain. This is five extra hearts by default. This is still being worked on!
  duration_seconds: 60

ores:
  coal_ore:
    enabled: true
    drop-mode: all  # all or random
    drops:
      - coal:1
    effects: []
    effects-mode: random # all or random

  iron_ore:
    enabled: true
    drop-mode: random
    drops:
      - iron_ingot:16
      - iron_sword:1
    effects: []
    effects-mode: random

  redstone_ore:
    enabled: true
    drop-mode: all
    drops: []
    effects: []
    effects-mode: random

  gold_ore:
    enabled: true
    drop-mode: random
    drops:
      - bow:1
      - arrow:2
      - cobweb:1
    effects: []
    effects-mode: random

  lapis_ore:
    enabled: true
    drop-mode: random
    drops:
      - lapis_lazuli:8
    effects:
      - type: SPEED
        duration: 200
        amplifier: 1
      - type: STRENGTH
        duration: 200
        amplifier: 0
      - type: JUMP_BOOST
        duration: 200
        amplifier: 1
    effects-mode: random

  diamond_ore:
    enabled: true
    drop-mode: random
    drops:
      - diamond_helmet:1
      - diamond_chestplate:1
      - diamond_leggings:1
      - diamond_boots:1
      - diamond_sword:1
    effects: []
    effects-mode: random

  emerald_ore:
    enabled: true
    drop-mode: all
    drops:
      - iron_sword:1
    effects: []
    effects-mode: random

# -----------------------------
# Enchantments Section 
# PMDevChris
# -----------------------------

enchantments:
  emerald_ore:
    items:
      iron_sword:
        chance: 100
        max_level: 3
        enchantments:
          - sharpness
# -----------------------------
# Instructions
# -----------------------------
# 1. To modify drops for an ore, edit ores.<ore_name>.drops
#    - Format: item_name:count
#    - Example: diamond_sword:1
#
# 2. To add enchantments to an item:
#    - Go to top-level enchantments.<ore_name>.items.<item_name>
#    - Set chance (0-100) for probability, max_level, and enchantments list
#
# 3. Effects:
#    - Use ores.<ore_name>.effects for potion effects
#    - Each effect: type, duration (ticks), amplifier
#    - Set effects-mode: "all" or "random"
#
# 4. You can add new ores by copying the pattern under "ores" and adding a matching section in "enchantments" if you want enchanted drops.
#
# 5. PMDevChris hashtags are included as comments for reference
```

### Installation

* Download the latest .phar file from Poggit.
* Place the .phar file in your server’s plugins/ folder.
* Start or reload the server.
* Edit the config.yml in plugin_data/HiveOreRewards/ to customize your ores, drops, enchantments, and effects.

### Support

* Plugin made by PMDevChris
* For questions or suggestions, open an issue on the Poggit repository.
