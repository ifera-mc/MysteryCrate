<?php
declare(strict_types=1);

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
use JackMD\MysteryCrate\Utils\Lang;
use JackMD\MysteryCrate\Utils\ParticleType;
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

class Main extends PluginBase{

	/** @var bool */
	private $notInUse = false;
	/** @var array */
	private $crates = [];
	/** @var array */
	private $crateDrops = [];
	/** @var array */
	private $crateBlocks = [];
	/** @var FloatingTextParticle[] */
	private $textParticles;
	/** @var Config */
	private $blocksConfig;

	public function onEnable(): void{
		Lang::init($this);

		$this->saveDefaultConfig();
		$this->initCrates();

		if($this->getConfig()->get("showParticle") !== false){
			if($this->getServer()->getLevelByName((string) $this->getConfig()->get("crateWorld")) !== null){
				$this->initParticleShow();
			}else{
				$this->getServer()->getLogger()->critical("Please set the crateWorld in the config.yml");
			}
		}
		$this->initTextParticle();

		$this->setNotInUse(true);

		$this->getServer()->getCommandMap()->register("mysterycrate", new KeyCommand($this));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getLogger()->info("Plugin Enabled.");
	}

	public function initCrates(): void{
		$this->saveResource("crates.yml");
		$this->saveResource("blocks.yml");
		$this->blocksConfig = new Config($this->getDataFolder() . "blocks.yml");

		$config = new Config($this->getDataFolder() . "crates.yml");
		foreach($config->getNested("crates") as $type => $values){
			$this->crates[$type] = $values;
			$this->crateDrops[$type] = $values["drops"];
			$this->crateBlocks[$values["block"]] = $type;
		}
	}

	public function initParticleShow(){
		$blocksConfig = $this->blocksConfig;
		$types = $this->getCrateTypes();
		$particleType = (string) $this->getConfig()->get("particleType");
		$particleTickRate = (int) $this->getConfig()->get("particleTickRate");

		foreach($types as $type){
			$x = $blocksConfig->get("$type.x");
			$y = $blocksConfig->get("$type.y");
			$z = $blocksConfig->get("$type.z");

			if(!empty($x)){
				$pos = new Vector3($x + 0.5, $y, $z + 0.5);
				$task = null;

				switch($particleType){
					case ParticleType::HELIX:
						$task = new Helix($this, $pos);
						break;
					case ParticleType::DOUBLE_HELIX:
						$task = new DoubleHelix($this, $pos);
						break;
					case ParticleType::CLOUD_RAIN:
						$task = new CloudRain($this, $pos);
						break;
					case ParticleType::TING:
						$task = new Ting($this, $pos);
						break;
					case ParticleType::CROWN:
						$task = new Crown($this, $pos);
						break;
				}

				if(!is_null($task)){
					$this->getScheduler()->scheduleRepeatingTask($task, $particleTickRate);
				}else{
					$this->getLogger()->error("Please set the particleType in config.yml correctly. Allowed types are CloudRain, Helix, DoubleHelix, Ting and Crown");
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function getCrateTypes(): array{
		return array_keys($this->crates);
	}

	public function initTextParticle(): void{
		$blocksConfig = $this->blocksConfig;
		$types = $this->getCrateTypes();

		foreach($types as $type){
			$text = $blocksConfig->get($type);
			$x = $blocksConfig->get($type . ".x");
			$y = $blocksConfig->get($type . ".y");
			$z = $blocksConfig->get($type . ".z");

			if(!empty($x)){
				$pos = new Vector3($x + 0.5, $y + 1, $z + 0.5);
				$this->textParticles[$type] = new FloatingTextParticle($pos, '', $text . TextFormat::RESET);
			}
		}
	}

	/**
	 * @param Player $player
	 */
	public function addParticles(Player $player): void{
		if(isset($this->textParticles)){
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
	public function setNotInUse(bool $notInUse): void{
		$this->notInUse = $notInUse;
	}

	/**
	 * @param string $type
	 * @return int
	 */
	public function getCrateDropAmount(string $type): int{
		return !$this->getCrateType($type) ? 0 : $this->crates[$type]["amount"];
	}

	/**
	 * @param string $type
	 * @return bool|array
	 */
	public function getCrateType(string $type){
		return isset($this->crates[$type]) ? $this->crates[$type] : false;
	}

	/**
	 * @param string $type
	 * @return null|array
	 */
	public function getCrateBlock(string $type){
		return $this->getCrateDrops($type) ? $this->crateBlocks[$type] : null;
	}

	/**
	 * @param string $type
	 * @return null|array
	 */
	public function getCrateDrops(string $type){
		return $this->getCrateType($type) ? $this->crateDrops[$type] : null;
	}

	/**
	 * @param int $id
	 * @param int $meta
	 * @return bool|string
	 */
	public function isCrateBlock(int $id, int $meta){
		return isset($this->crateBlocks[$id . ":" . $meta]) ? $this->crateBlocks[$id . ":" . $meta] : false;
	}

	/**
	 * @param Item $item
	 * @return bool
	 */
	public function isCrateKey(Item $item): bool{
		$values = explode(":", $this->getConfig()->getNested("key"));

		return ($values[0] === $item->getId() && $values[1] === $item->getDamage() && !is_null($keyType = $item->getNamedTagEntry("KeyType"))) ? $keyType->getValue() : false;
	}

	/**
	 * @param Player $player
	 * @param string $type
	 * @param int    $amount
	 * @return bool
	 */
	public function giveKey(Player $player, string $type, int $amount): bool{
		if(is_null($this->getCrateDrops($type))){
			return false;
		}

		$keyID = (string) $this->getConfig()->get("key");

		$key = Item::fromString($keyID);
		$key->setCount($amount);
		$key->setLore([$this->getConfig()->get("lore")]);
		$key->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1)));
		$key->setCustomName(ucfirst($type . " Key"));
		$key->setNamedTagEntry(new StringTag("KeyType", $type));

		$player->getInventory()->addItem($key);

		return true;
	}

	/**
	 * @return Config
	 */
	public function getBlocksConfig(): Config{
		return $this->blocksConfig;
	}

	/**
	 * @return FloatingTextParticle[]
	 */
	public function getTextParticles(): array{
		return $this->textParticles;
	}
}
