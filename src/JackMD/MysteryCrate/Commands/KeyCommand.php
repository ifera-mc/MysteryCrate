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
use pocketmine\Player;
use pocketmine\utils\TextFormat;

/**
 * Class KeyCommand
 * @package MysteryCrate\Commands
 */
class KeyCommand extends PluginCommand
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
        $this->setDescription("Give a crate key");
        $this->setUsage("/key [player] [amount]");
        $this->setPermission("mc.command.key");
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
        if ($sender->isOp() || $sender->hasPermission("mc.command.key")) {
            $plugin = $this->getPlugin();
            if ($plugin instanceof Main) {
                if (!isset($args[0])) {
                    $sender->sendMessage(TextFormat::RED . "Usage: /key [player] [amount]");
                    return false;
                }
                $target = $sender;
                if (isset($args[0])) {
                    $target = $plugin->getServer()->getPlayer($args[0]);
                    if (!$target instanceof Player) {
                        $sender->sendMessage(TextFormat::RED . "Invalid player. Try again.");
                        return false;
                    }
                } else {
                    if (!$target instanceof Player) {
                        $sender->sendMessage(TextFormat::RED . "Please specify a player.");
                        return false;
                    }
                }
                if (isset($args[1]) and is_numeric($args[1])) {
                    $amount = (int)$args[1];
                } else {
                    $amount = $plugin->getAmount();
                }
                $plugin->giveKey($target, $amount);

                $keyName = $plugin->getConfig()->getNested("keyName");

                $sender->sendMessage(TextFormat::GOLD . $keyName . TextFormat::GREEN . " key has been given.");

                return true;
            }
        }else{
            $sender->sendMessage(TextFormat::RED . "You don't have permission to use this command.");
        }
        return true;
    }
}