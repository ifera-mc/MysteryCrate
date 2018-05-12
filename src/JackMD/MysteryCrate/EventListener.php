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

namespace JackMD\MysteryCrate;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\level\particle\LavaParticle;
use pocketmine\math\Vector3;
use pocketmine\tile\Chest as ChestTile;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class EventListener implements Listener
{
	public $plugin;

	/**
	 * EventListener constructor.
	 *
	 * @param Main $plugin
	 */
	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
	}

	/**
	 * @param BlockBreakEvent $event
	 */
	public function onBreak(BlockBreakEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if (!$player->isOp()) {
			if ($this->plugin->isCrateBlock($block->getId(), $block->getDamage())) {
				if ($block->getLevel()->getBlock($block->add(0, 1))->getId() == Block::CHEST) {
					if (!$player->hasPermission("mc.crates.destroy")) {
						$player->sendMessage(TextFormat::RED . "You do not have permission to destroy a crate.");
						$event->setCancelled();
					}
				}
			} elseif ($block->getId() == Block::CHEST) {
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				if ($this->plugin->isCrateBlock($b->getId(), $b->getDamage())) {
					if (!$player->hasPermission("mc.crates.destroy")) {
						$player->sendMessage(TextFormat::RED . "You do not have permission to destroy a crate.");
						$event->setCancelled();
					}
				}
			}
		}
	}

	/**
	 * @param BlockPlaceEvent $event
	 */
	public function onPlace(BlockPlaceEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if (!$player->isOp()) {
			if ($this->plugin->isCrateBlock($block->getId(), $block->getDamage())) {
				if ($block->getLevel()->getBlock($block->add(0, 1))->getId() == Block::CHEST) {
					if (!$player->hasPermission("mc.crates.create")) {
						$player->sendMessage(TextFormat::RED . "You do not have permission to create a crate.");
						$event->setCancelled();
					}
				}
			} elseif ($block->getId() == Block::CHEST) {
				$b = $block->getLevel()->getBlock($block->subtract(0, 1));
				if ($this->plugin->isCrateBlock($b->getId(), $b->getDamage())) {
					if (!$player->hasPermission("mc.crates.create")) {
						$player->sendMessage(TextFormat::RED . "You do not have permission to create a crate.");
						$event->setCancelled();
					}
				}
			}
		}
	}

	/**
	 * @param PlayerInteractEvent $event
	 */
	public function onInteract(PlayerInteractEvent $event)
	{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$this->plugin->task->block = $block;
		$b = $block->getLevel()->getBlock($block->subtract(0 , 1));
		$level = $block->getLevel()->getFolderName();
		$item = $event->getItem();

		if ($block->getId() == Block::CHEST && ($type = $this->plugin->isCrateBlock($b->getId() , $b->getDamage())) !== false) {
			if ($level === $this->plugin->getConfig()->get("crateWorld")) {

				if (!$player->hasPermission("mc.crates.use")) {
					$event->setCancelled();
					$player->sendMessage(TextFormat::RED . "You do not have permission to use a crate.");
				} else {
					if (!($keytype = $this->plugin->isCrateKey($item)) || $keytype !== $type) {
						$event->setCancelled();
						$player->sendMessage(TextFormat::RED . "You require a " . ucfirst($type) . " key to open this crate.");
					} else {

						$event->setCancelled(false);
						if ($this->plugin->isNotInUse()) {
							$this->plugin->setNotInUse(false);
							global $chest;
							$chest = $event->getPlayer()->getLevel()->getTile(new Vector3($event->getBlock()->getX() , $event->getBlock()->getY() , $event->getBlock()->getZ()));;

							if ($chest instanceof ChestTile) {
								$chest->getInventory()->clearAll();
								$this->plugin->task->chest = $chest;
								$this->plugin->task->player = $player;
								$this->plugin->task->t_delay = 2 * 20;
								$item = $player->getInventory()->getItemInHand();
								$item->setCount($item->getCount() - 1);
								$item->setDamage($item->getDamage());
								$event->getPlayer()->getInventory()->setItemInHand($item);
								$this->plugin->task->scheduler = $this->plugin->getServer()->getScheduler();
								$this->plugin->getServer()->getScheduler()->scheduleRepeatingTask($this->plugin->task , 5);

								//Particle upon opening chest
								$cx = $block->getX() + 0.5;
								$cy = $block->getY() + 1.2;
								$cz = $block->getZ() + 0.5;
								$radius = (int) 1;
								for ($i = 0 ; $i < 361 ; $i += 1.1) {
									$x = $cx + ($radius * cos($i));
									$z = $cz + ($radius * sin($i));
									$pos = new Vector3($x , $cy , $z);
									$block->level->addParticle(new LavaParticle($pos));
								}
							}
						} else {
							$event->setCancelled();
							$player->sendMessage(TextFormat::RED . "The crate is in use. Please wait...");
						}
					}
				}
			}
		}
	}

	/**
	 * @param InventoryTransactionEvent $event
	 */
	public function onTransaction(InventoryTransactionEvent $event)
	{
		global $chest;
		/** @var Tile|ChestTile $tile */
		$tile = $chest;
		$x = $tile->getX();
		$y = $tile->getY();
		$z = $tile->getZ();

		$pos = new Vector3($x , $y , $z);

		$tileChest = $event->getTransaction()->getSource()->getPlayer()->getLevel()->getTile($pos);

		if ($tileChest === $tile) {
			if ($tile->getLevel()->getName() === $this->plugin->getConfig()->get("crateWorld")) {
				if ($tileChest->getY() === $tile->getY()) {
					if ($tileChest instanceof ChestTile) {
						foreach ($event->getTransaction()->getActions() as $action) {
							if ($action instanceof SlotChangeAction) {
								$inventory = $action->getInventory();
								if ($inventory instanceof ChestInventory) {
									if ($this->plugin->task !== null && $this->plugin->task->isCanTakeItem()) {
										$event->setCancelled(true);
									} else {
										$event->setCancelled(true);
									}
								}
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
	public function onInventoryClose(InventoryCloseEvent $event)
	{
		/** @var ChestTile $chest */
		global $chest;
		if ($event->getInventory() instanceof ChestInventory) {
			$cpos = new Vector3($chest->getX() , $chest->getY() , $chest->getZ());
			$chestTile = $event->getPlayer()->getLevel()->getTile($cpos);

			if ($chestTile instanceof ChestTile) {
				$this->plugin->setNotInUse(true);
				$this->plugin->getServer()->getScheduler()->cancelTask($this->plugin->task->getTaskId());
			}
		}
	}
}