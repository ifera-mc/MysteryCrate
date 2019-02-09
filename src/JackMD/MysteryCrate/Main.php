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
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate;

use JackMD\MysteryCrate\command\KeyCommand;
use JackMD\MysteryCrate\particle\CloudRain;
use JackMD\MysteryCrate\particle\Crown;
use JackMD\MysteryCrate\particle\DoubleHelix;
use JackMD\MysteryCrate\particle\Helix;
use JackMD\MysteryCrate\particle\Ting;
use JackMD\MysteryCrate\utils\Lang;
use JackMD\MysteryCrate\utils\ParticleType;
use JackMD\UpdateNotifier\UpdateNotifier;
use muqsit\invmenu\InvMenuHandler;
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

	/** @var int */
	private const CRATES_VERSION = 1;
	/** @var int */
	private const CONFIG_VERSION = 2;

	/** @var bool */
	private $notInUse = false;
	/** @var array */
	private $crates = [];
	/** @var array */
	private $crateDrops = [];
	/** @var array */
	private $crateBlocks = [];
	/** @var array */
	private $crateBroadcast = [];
	/** @var array */
	private $crateBroadcastMessage = [];
	/** @var null|FloatingTextParticle[] */
	private $textParticles;
	/** @var Config */
	private $blocksConfig;

	public function onLoad(): void{
		$this->checkVirions();

		Lang::init($this);

		$this->saveDefaultConfig();
		$this->initCrates();
		$this->checkConfigs();
		$this->setNotInUse(true);

		UpdateNotifier::checkUpdate($this, $this->getDescription()->getName(), $this->getDescription()->getVersion());
	}

	/**
	 * Checks if the required virions/libraries are present before enabling the plugin.
	 */
	private function checkVirions(): void{
		if(!class_exists(UpdateNotifier::class)){
			throw new \RuntimeException("MysteryCrate plugin will only work if you use the plugin phar from Poggit.");
		}
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
			$this->crateBroadcast[$type] = $values["broadcast"]["enable"];
			$this->crateBroadcastMessage[$type] = $values["broadcast"]["command"];
		}
	}

	/**
	 * Check if the configs are up-to-date.
	 */
	private function checkConfigs(): void{
		$cratesConfig = new Config($this->getDataFolder() . "crates.yml", Config::YAML);
		if((!$cratesConfig->exists("crates-version")) || ($cratesConfig->get("crates-version") !== self::CRATES_VERSION)){
			rename($this->getDataFolder() . "crates.yml", $this->getDataFolder() . "crates_old.yml");
			$this->saveResource("crates.yml");
			$this->getLogger()->critical("Your crates.yml file is outdated.");
			$this->getLogger()->notice("Your old crates.yml has been saved as crates_old.yml and a new crates.yml file has been generated. Please update accordingly.");
		}

		$config = $this->getConfig();
		if((!$config->exists("config-version")) || ($config->get("config-version") !== self::CONFIG_VERSION)){
			rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config_old.yml");
			$this->saveResource("config.yml");
			$this->getLogger()->critical("Your config.yml file is outdated.");
			$this->getLogger()->notice("Your old config.yml has been saved as config_old.yml and a new config.yml file has been generated. Please update accordingly.");
		}
	}

	public function onEnable(): void{
		$this->initParticles();
		$this->initTextParticle();

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

		$this->getServer()->getCommandMap()->register("mysterycrate", new KeyCommand($this));
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getLogger()->info("Plugin Enabled.");
	}

	private function initParticles(): void{
		if($this->getConfig()->get("showParticle")){
			$crateWorld = (string) $this->getConfig()->get("crateWorld");
			if(!$this->getServer()->isLevelLoaded($crateWorld)){
				$this->getServer()->loadLevel($crateWorld);
			}
			if($this->getServer()->getLevelByName($crateWorld) !== null){
				$this->initParticleShow();
			}else{
				$this->getServer()->getLogger()->critical("Please set the crateWorld in the config.yml. Or make sure that the world exists and is loaded.");
			}
		}
	}

	private function initParticleShow(): void{
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
	 * @return bool|string
	 */
	public function isCrateKey(Item $item){
		$values = explode(":", $this->getConfig()->getNested("key"));

		return ((int) $values[0] === $item->getId()) && ((int) $values[1] === $item->getDamage()) && (!is_null($keyType = $item->getNamedTagEntry("KeyType"))) ? $keyType->getValue() : false;
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public function isBroadcastEnabled(string $type): bool{
		return $this->crateBroadcast[$type];
	}

	/**
	 * @param string $type
	 * @return string
	 */
	public function getBroadcastMessage(string $type): string{
		return $this->crateBroadcastMessage[$type];
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
	 * @return null|FloatingTextParticle[]
	 */
	public function getTextParticles(): ?array{
		return $this->textParticles;
	}
}
