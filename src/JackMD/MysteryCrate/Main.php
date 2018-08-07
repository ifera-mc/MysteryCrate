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
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate;

use JackMD\MysteryCrate\Commands\KeyCommand;
use JackMD\MysteryCrate\Particles\CloudRain;
use JackMD\MysteryCrate\Particles\Crown;
use JackMD\MysteryCrate\Particles\DoubleHelix;
use JackMD\MysteryCrate\Particles\Helix;
use JackMD\MysteryCrate\Particles\Ting;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class Main
 *
 * @package MysteryCrate
 */
class Main extends PluginBase{
	
	public $notInUse = false;
	/** @var UpdaterEvent */
	public $task;
	public $crates, $crateDrops, $crateBlocks;
	/** @var FloatingTextParticle[] */
	public $textParticles;
	/** @var Config */
	public $blocks;
	private $key;
	
	public function onEnable(): void{
		$this->task = new UpdaterEvent($this);
		if(!is_dir($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}
		$this->saveDefaultConfig();
		$this->initCrates();
		if($this->getConfig()->get("showParticle") !== false){
			if($this->getServer()->getLevelByName($this->getConfig()->get("crateWorld")) !== null){
				$this->initParticleShow();
			}else{
				$this->getServer()->getLogger()->critical("Please set the crateWorld in the config.yml");
			}
		}
		$this->initTextParticle();
		$this->setNotInUse(true);
		$this->key = $this->getConfig()->getNested("key");
		$this->getServer()->getCommandMap()->register("key", new KeyCommand("key", $this), "key");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getLogger()->info("Plugin Enabled.");
	}
	
	public function initCrates(){
		$this->saveResource("crates.yml");
		$file = new Config($this->getDataFolder() . "crates.yml");
		foreach($file->getNested("crates") as $type => $values){
			$this->crates[$type] = $values;
			$this->crateDrops[$type] = $values["drops"];
			$this->crateBlocks[$values["block"]] = $type;
		}
		$this->saveResource("blocks.yml");
		$this->blocks = new Config($this->getDataFolder() . "blocks.yml");
	}
	
	public function initParticleShow(){
		$positions = $this->blocks;
		$types = $this->getCrateTypes();
		$particleType = $this->getConfig()->get("particleType");
		$particleTickRate = $this->getConfig()->get("particleTickRate");
		foreach($types as $type){
			$x = $positions->get("$type.x");
			$y = $positions->get("$type.y");
			$z = $positions->get("$type.z");
			if(!empty($x)){
				$pos = new Vector3($x + 0.5, $y, $z + 0.5);
				$task = "";
				if($particleType == "Helix"){
					$task = new Helix($this, $pos);
				}elseif($particleType == "DoubleHelix"){
					$task = new DoubleHelix($this, $pos);
				}elseif($particleType == "CloudRain"){
					$task = new CloudRain($this, $pos);
				}elseif($particleType == "Ting"){
					$task = new Ting($this, $pos);
				}elseif($particleType == "Crown"){
					$task = new Crown($this, $pos);
				}
				if($task !== ""){
					$this->getScheduler()->scheduleRepeatingTask($task, $particleTickRate);
				}else{
					$this->getLogger()->debug(TextFormat::DARK_RED . "Please set the particleType in config.yml correctly. Allowed types are CloudRain, Helix, DoubleHelix, Ting, Crown");
				}
			}
		}
	}
	
	/**
	 * @return array
	 */
	public function getCrateTypes(){
		return array_keys($this->crates);
	}
	
	public function initTextParticle(){
		$positions = $this->blocks;
		$types = $this->getCrateTypes();
		foreach($types as $type){
			$text = $positions->get($type);
			$x = $positions->get($type . ".x");
			$y = $positions->get($type . ".y");
			$z = $positions->get($type . ".z");
			if(!empty($x)){
				$pos = new Vector3($x + 0.5, $y + 1, $z + 0.5);
				$this->textParticles[$type] = new FloatingTextParticle($pos, '', $text . TextFormat::RESET);
			}
		}
	}
	
	public function addParticles(Player $player){
		$particles = array_values($this->textParticles);
		foreach($particles as $particle){
			if($particle instanceof FloatingTextParticle){
				foreach($particle->encode() as $packet){
					$particle->setInvisible(false);
					$player->dataPacket($packet);
				}
			}
		}
	}
	
	/**
	 * @return bool
	 */
	public function isNotInUse(): bool{
		return $this->notInUse;
	}
	
	/**
	 * @param bool $notInUse
	 */
	public function setNotInUse(bool $notInUse){
		$this->notInUse = $notInUse;
	}
	
	/**
	 * @param string $type
	 * @return int
	 */
	public function getCrateDropAmount(string $type){
		return !$this->getCrateType($type) ? 0 : $this->crates[$type]["amount"];
	}
	
	/**
	 * @param string $type
	 * @return bool
	 */
	public function getCrateType(string $type){
		return isset($this->crates[$type]) ? $this->crates[$type] : false;
	}
	
	/**
	 * @param string $type
	 * @return null
	 */
	public function getCrateBlock(string $type){
		return !$this->getCrateDrops($type) ? null : $this->crateBlocks[$type];
	}
	
	/**
	 * @param string $type
	 * @return null
	 */
	public function getCrateDrops(string $type){
		return !$this->getCrateType($type) ? null : $this->crateDrops[$type];
	}
	
	/**
	 * @param int $id
	 * @param int $meta
	 * @return bool
	 */
	public function isCrateBlock(int $id, int $meta){
		return isset($this->crateBlocks[$id . ":" . $meta]) ? $this->crateBlocks[$id . ":" . $meta] : false;
	}
	
	/**
	 * @param Item $item
	 * @return bool
	 */
	public function isCrateKey(Item $item){
		$values = explode(":", $this->key);
		return ($values[0] == $item->getId() && $values[1] == $item->getDamage() && !is_null($keytype = $item->getNamedTagEntry("KeyType"))) ? $keytype->getValue() : false;
	}
	
	/**
	 * @param Player $player
	 * @param string $type
	 * @param int    $amount
	 * @return bool
	 */
	public function giveKey(Player $player, string $type, int $amount){
		if(is_null($this->getCrateDrops($type))){
			return false;
		}
		$keyID = $this->getConfig()->get("key");
		$key = Item::fromString($keyID);
		$key->setCount($amount);
		$key->setLore([$this->getConfig()->get("lore")]);
		$key->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
		$key->setCustomName(ucfirst($type . " Key"));
		$key->setNamedTagEntry(new StringTag("KeyType", $type));
		$player->getInventory()->addItem($key);
		return true;
	}
}
