<?php

/**
 * MysteryCrate, a Crate plugin for PocketMine-MP
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * This software is distributed under "GNU General Public License v3.0".
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program.  If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 *
 * ----------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate\Commands;

use JackMD\MysteryCrate\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

/**
 * Class KeyCommand
 * @package MysteryCrate\Commands
 */
class xyzCommand extends PluginCommand
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Main
     */
    private $plugin;

    /**
     * KeyCommand constructor.
     * @param string $name
     * @param Main $plugin
     */
    public function __construct(string $name, Main $plugin)
    {
        parent::__construct($name, $plugin);
        $this->setDescription("Get XYZ coordinates of a block!");
        $this->setUsage("/xyz");
        $this->setPermission("mysterycrate.command.xyz");
        $this->name = $name;
        $this->plugin = $plugin;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $plugin = $this->getPlugin();
        $key = Item::get(Item::GOLD_AXE, 0, 1);
        $key->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, 1))); //Glowing key effect
        $key->setCustomName(TextFormat::BOLD . TextFormat::GOLD . "XYZ" . TextFormat::RED . " Locator" . TextFormat::RESET);
        $key->setLore(['Tap the block whose', 'location you want to find',]);

        if ($plugin instanceof Main) {
            if (!isset($args[0])) {
                if ($sender instanceof Player) {
                    $sender->getInventory()->addItem($key);
                    $sender->sendMessage(TextFormat::GREEN . "You have recieved " . TextFormat::BOLD . TextFormat::GOLD . "XYZ" . TextFormat::RED . " Locator." . TextFormat::RESET);
                    $sender->sendMessage(TextFormat::LIGHT_PURPLE . "Tap a block with it to find its coordinates.");
                }
            }
        }
        return true;
    }
}