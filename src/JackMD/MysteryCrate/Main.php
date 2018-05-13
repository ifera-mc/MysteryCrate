<?php

/**
 * ___  ___          _                  _____           _
 * |  \/  |         | |                /  __ \         | |
 * | .  . |_   _ ___| |_ ___ _ __ _   _| /  \/_ __ __ _| |_ ___
 * | |\/| | | | / __| __/ _ \ '__| | | | |   | '__/ _` | __/ _ \
 * | |  | | |_| \__ \ ||  __/ |  | |_| | \__/\ | | (_| | ||  __/
 * \_|  |_/\__, |___/\__\___|_|   \__, |\____/_|  \__,_|\__\___|
 *          __/ |                  __/ |
 *         |___/                  |___/  By @JackMD for PMMP
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
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate;

use JackMD\MysteryCrate\Commands\KeyCommand;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

/**
 * Class Main
 *
 * @package MysteryCrate
 */
class Main extends PluginBase
{
	public $task;
	public $notInUse = true;
	public $crates;
	public $crateDrops;
	public $crateBlocks;
	private $key;

	public function onEnable() : void
	{
		$this->task = new UpdaterEvent($this);

		if (!is_dir($this->getDataFolder())) {
			mkdir($this->getDataFolder());
		}
		$this->initCrates();
		$this->saveDefaultConfig();
		$this->key = $this->getConfig()->getNested("key");
		$this->getServer()->getCommandMap()->register("key" , new KeyCommand("key" , $this) , "key");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this) , $this);
		$this->getLogger()->info("Plugin Enabled.");

	}

	public function initCrates()
	{
		$this->saveResource("crates.yml");
		$file = new Config($this->getDataFolder() . "crates.yml");
		foreach ($file->getNested("crates") as $type => $values) {
			$this->crates[$type] = $values;
			$this->crateDrops[$type] = $values["drops"];
			$this->crateBlocks[$values["block"]] = $type;
		}
	}

	/**
	 * @return bool
	 */
	public function isNotInUse() : bool
	{
		return $this->notInUse;
	}

	/**
	 * @param bool $notInUse
	 */
	public function setNotInUse(bool $notInUse)
	{
		$this->notInUse = $notInUse;
	}

	/**
	 * @return array
	 */
	public function getCrateTypes()
	{
		return array_keys($this->crates);
	}

	/**
	 * @param string $type
	 * @return int
	 */
	public function getCrateDropAmount(string $type)
	{
		return !$this->getCrateType($type) ? 0 : $this->crates[$type]["amount"];
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public function getCrateType(string $type)
	{
		return isset($this->crates[$type]) ? $this->crates[$type] : false;
	}

	/**
	 * @param string $type
	 * @return null
	 */
	public function getCrateBlock(string $type)
	{
		return !$this->getCrateDrops($type) ? null : $this->crateBlocks[$type];
	}

	/**
	 * @param string $type
	 * @return null
	 */
	public function getCrateDrops(string $type)
	{
		return !$this->getCrateType($type) ? null : $this->crateDrops[$type];
	}

	/**
	 * @param int $id
	 * @param int $meta
	 * @return bool
	 */
	public function isCrateBlock(int $id , int $meta)
	{
		return isset($this->crateBlocks[$id . ":" . $meta]) ? $this->crateBlocks[$id . ":" . $meta] : false;
	}

	/**
	 * @param Item $item
	 * @return bool
	 */
	public function isCrateKey(Item $item)
	{
		$values = explode(":" , $this->key);

		return ($values[0] == $item->getId() && $values[1] == $item->getDamage() && !is_null($keytype = $item->getNamedTagEntry("KeyType"))) ? $keytype->getValue() : false;
	}

	/**
	 * @param Player $player
	 * @param string $type
	 * @param int    $amount
	 * @return bool
	 */
	public function giveKey(Player $player , string $type , int $amount)
	{
		if (is_null($this->getCrateDrops($type))) {
			return false;
		}
		$key = Item::get(Item::PAPER);
		$key->setCount($amount);
		$key->setLore([$this->getConfig()->get("descOne") , $this->getConfig()->get("desTwo")]);
		$key->addEnchantment(new EnchantmentInstance(new Enchantment(255 , "" , Enchantment::RARITY_COMMON , Enchantment::SLOT_ALL , 1)));
		$key->setCustomName(ucfirst($type . " Key"));
		$key->setNamedTagEntry(new StringTag("KeyType" , $type));
		$player->getInventory()->addItem($key);

		return true;
	}
}
