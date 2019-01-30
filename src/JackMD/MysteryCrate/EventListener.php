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

use JackMD\MysteryCrate\Utils\Lang;
use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\level\particle\LavaParticle;
use pocketmine\math\Vector3;
use pocketmine\tile\Chest as ChestTile;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{

	/** @var Main */
	private $plugin;
	/** @var UpdaterEvent */
	private $updaterTask;
	
	/**
	 * EventListener constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}
	
	/**
	 * @param BlockBreakEvent $event
	 * @priority        HIGHEST
	 */
	public function onBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if(!($player->hasPermission("mc.crates.destroy"))){
			if($this->plugin->isCrateBlock($block->getId(), $block->getDamage())){
				if($block->getLevel()->getBlock($block->add(0, 1))->getId() == Block::CHEST){
					$player->sendMessage(Lang::$no_perm_destroy);
					$event->setCancelled();
				}
			}elseif($block->getId() == Block::CHEST){
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				if($this->plugin->isCrateBlock($b->getId(), $b->getDamage())){
					$player->sendMessage(Lang::$no_perm_destroy);
					$event->setCancelled();
				}
			}
		}else{
			if($block->getId() == Block::CHEST){
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				if($type = $this->plugin->isCrateBlock($b->getId(), $b->getDamage())){
					$config = $this->plugin->getBlocksConfig();
					if(!empty($config->get($type))){
						$config->remove($type);
						$config->remove($type . ".x");
						$config->remove($type . ".y");
						$config->remove($type . ".z");
						$config->save();
						if(isset($this->plugin->getTextParticles()[$type])){
							unset($this->plugin->getTextParticles()[$type]);
							$this->plugin->initTextParticle();
						}
						$player->sendMessage(Lang::$crate_destroy_successful);
					}
				}
			}
		}
	}
	
	/**
	 * @param BlockPlaceEvent $event
	 * @priority        HIGHEST
	 */
	public function onPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if(!($player->hasPermission("mc.crates.create"))){
			if($this->plugin->isCrateBlock($block->getId(), $block->getDamage())){
				if($block->getLevel()->getBlock($block->add(0, 1))->getId() == Block::CHEST){
					$player->sendMessage(Lang::$no_perm_create);
					$event->setCancelled();
				}
			}elseif($block->getId() == Block::CHEST){
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				if($this->plugin->isCrateBlock($b->getId(), $b->getDamage())){
					$player->sendMessage(Lang::$no_perm_create);
					$event->setCancelled();
				}
			}
		}else{
			if($block->getId() == Block::CHEST){
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				if($type = $this->plugin->isCrateBlock($b->getId(), $b->getDamage())){
					$x = $block->getX();
					$y = $block->getY();
					$z = $block->getZ();
					$config = $this->plugin->getBlocksConfig();
					if(empty($config->get($type))){
						$config->set($type, TextFormat::GOLD . ucfirst($type) . TextFormat::GREEN . " Crate");
						$config->set($type . ".x", $x);
						$config->set($type . ".y", $y);
						$config->set($type . ".z", $z);
						$config->save();
						$player->sendMessage(Lang::$crate_place_successful);
					}
				}
			}
		}
	}
	
	/**
	 * @param PlayerInteractEvent $event
	 * @priority        HIGHEST
	 */
	public function onInteract(PlayerInteractEvent $event){
		$levelName = (string) $this->plugin->getConfig()->get("crateWorld");
		$lev = $this->plugin->getServer()->getLevelByName($levelName);
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$b = $block->getLevel()->getBlock($block->subtract(0, 1));
		$item = $event->getItem();
		if($player->getLevel() === $lev){
			if(($block->getId() == Block::CHEST) && ($type = $this->plugin->isCrateBlock($b->getId(), $b->getDamage())) !== false){
				if(!$player->hasPermission("mc.crates.use")){
					$event->setCancelled();
					$player->sendMessage(Lang::$no_perm_use_crate);
					return;
				}else{
					if(!($keytype = $this->plugin->isCrateKey($item)) || $keytype !== $type){
						$event->setCancelled();
						$player->sendMessage(str_replace(["%TYPE%"], [ucfirst($type)], Lang::$no_key));
						return;
					}elseif($player->isSneaking()){
						$event->setCancelled();
						$player->sendMessage(Lang::$error_sneak);
						return;
					}
					if($this->plugin->isNotInUse()){
						$event->setCancelled(false);
						$this->plugin->setNotInUse(false);
						$chestTile = $player->getLevel()->getTile(new Vector3($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ()));;
						if($chestTile instanceof ChestTile){
							$chestTile->getInventory()->clearAll();
							$t_delay = $this->plugin->getConfig()->get("tickDelay") * 20;
							$this->updaterTask = $updaterTask = new UpdaterEvent($this->plugin, $player, $chestTile, $t_delay);
							$item = $player->getInventory()->getItemInHand();
							$item->setCount($item->getCount() - 1);
							$item->setDamage($item->getDamage());
							$player->getInventory()->setItemInHand($item);
							$this->plugin->getScheduler()->scheduleRepeatingTask($updaterTask, 5);

							if($this->plugin->isBroadcastEnabled($type)){
								$cmd = $this->plugin->getBroadcastMessage($type);
								$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), str_replace("%PLAYER%", $player->getName(), $cmd));
							}

							//Particle upon opening chest
							$cx = $block->getX() + 0.5;
							$cy = $block->getY() + 1.2;
							$cz = $block->getZ() + 0.5;
							$radius = (int) 1;
							for($i = 0; $i < 361; $i += 1.1){
								$x = $cx + ($radius * cos($i));
								$z = $cz + ($radius * sin($i));
								$pos = new Vector3($x, $cy, $z);
								$block->level->addParticle(new LavaParticle($pos));
							}
						}
					}else{
						$event->setCancelled(true);
						$player->sendMessage(Lang::$error_crate_in_use);
						return;
					}
				}
			}
		}
	}
	
	/**
	 * @param InventoryTransactionEvent $event
	 * @priority        HIGHEST
	 */
	public function onTransaction(InventoryTransactionEvent $event){
		$levelName = (string) $this->plugin->getConfig()->get("crateWorld");
		$lev = $this->plugin->getServer()->getLevelByName($levelName);
		$player = $event->getTransaction()->getSource();
		if($player->getLevel() === $lev){
			foreach($event->getTransaction()->getActions() as $action){
				if($action instanceof SlotChangeAction){
					$cInv = $action->getInventory();
					if($cInv instanceof ChestInventory){
						$pos = $cInv->getHolder();
						$block = $lev->getBlock($pos);
						if($block->getId() == Block::CHEST){
							$b = $block->getLevel()->getBlock($block->subtract(0, 1));
							if($this->plugin->isCrateBlock($b->getId(), $b->getDamage())){
								$event->setCancelled(true);
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * @param InventoryCloseEvent $event
	 */
	public function onInventoryClose(InventoryCloseEvent $event){
		$levelName = (string) $this->plugin->getConfig()->get("crateWorld");
		$lev = $this->plugin->getServer()->getLevelByName($levelName);
		$che = $event->getInventory();
		$player = $event->getPlayer();
		if($player->getLevel() === $lev){
			if($che instanceof ChestInventory){
				$pos = $che->getHolder();
				$block = $lev->getBlock($pos);
				if($block->getId() == Block::CHEST){
					$b = $block->getLevel()->getBlock($block->subtract(0, 1));
					if($this->plugin->isCrateBlock($b->getId(), $b->getDamage())){
						$this->plugin->setNotInUse(true);
						$this->plugin->getScheduler()->cancelTask($this->updaterTask->getTaskId());
					}
				}
			}
		}
	}
	
	/**
	 * @param PlayerJoinEvent $event
	 */
	public function onJoin(PlayerJoinEvent $event){
		$this->plugin->addParticles($event->getPlayer());
	}
}
