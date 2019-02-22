<?php
declare(strict_types = 1);

/**
 * ___  ___          _                  _____           _
 * |  \/  |         | |                /  __ \         | |
 * | .  . |_   _ ___| |_ ___ _ __ _   _| /  \/_ __ __ _| |_ ___
 * | |\/| | | | / __| __/ _ \ '__| | | | |   | '__/ _` | __/ _ \
 * | |  | | |_| \__ \ ||  __/ |  | |_| | \__/\ | | (_| | ||  __/
 * \_|  |_/\__, |___/\__\___|_|   \__, |\____/_|  \__,_|\__\___|
 *          __/ |                  __/ |
 *         |___/                  |___/  By @JackMD for PMMP
 *
 * MysteryCrate, a Crate plugin for PocketMine-MP
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * MysteryCrate is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate\command;

use JackMD\MysteryCrate\Main;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class KeyAllCommand extends PluginCommand{

	/** @var Main */
	private $plugin;

	/**
	 * KeyAllCommand constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		parent::__construct("keyall", $plugin);
		$this->setDescription("Give a crate key to all the players on the server.");
		$this->setUsage("/keyall [type] [amount]");
		$this->setPermission("mc.command.keyall");

		$this->plugin = $plugin;
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 * @return bool|mixed
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender) or $this->checkArgs($args, $sender)){
			return true;
		}

		$keyAmount = (isset($args[1]) and is_numeric($args[1])) ? (int)$args[1] : 1;
		$lowercaseCrateType = strtolower($args[0]);

		foreach($this->plugin->getServer()->getOnlinePlayers() as $target){
			if($target->isOnline()){
				$this->plugin->giveKey($target, $lowercaseCrateType, $keyAmount);
				$target->sendMessage("Everyone has been give a " . ucfirst($lowercaseCrateType) . " key.");
			}
		}

		$sender->sendMessage(TextFormat::GREEN . ucfirst($lowercaseCrateType) . " key has been given to everyone.");

		return true;
	}

	/**
	 * Returns true when the args are not valid, false when everything is okay.
	 *
	 * @param array         $args
	 * @param CommandSender $sender
	 * @return bool
	 */
	private function checkArgs(array $args, CommandSender $sender): bool{
		if(!isset($args[0])){
			$sender->sendMessage(TextFormat::RED . "Usage: /keyall [type] [amount]");
		}elseif(!$this->plugin->getCrateType(strtolower($args[0]))){
			$sender->sendMessage(TextFormat::RED . "Invalid crate type.");
		}else{
			return false;
		}

		return true;
	}
}