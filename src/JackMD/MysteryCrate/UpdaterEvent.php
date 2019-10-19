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

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use JackMD\MysteryCrate\lang\Lang;
use muqsit\invmenu\inventories\BaseFakeInventory;
use muqsit\invmenu\InvMenu;
use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class UpdaterEvent extends Task{

	/** @var Main */
	private $plugin;
	/** @var int */
	private $t_delay;
	/** @var Player */
	private $player;
	/** @var Block */
	private $chestBlock;
	/** @var InvMenu */
	private $crateMenu;

	/**
	 * UpdaterEvent constructor.
	 *
	 * @param Main   $plugin
	 * @param Player $player
	 * @param Block  $chestBlock
	 * @param int    $t_delay
	 */
	public function __construct(Main $plugin, Player $player, Block $chestBlock, int $t_delay){
		$this->plugin = $plugin;
		$this->player = $player;
		$this->chestBlock = $chestBlock;
		$this->t_delay = $t_delay;

		$crateMenu = InvMenu::create(InvMenu::TYPE_CHEST);
		$crateMenu->readonly();
		$crateMenu->setInventoryCloseListener([$this, 'closeInventory']);
		$crateMenu->send($player);

		$this->crateMenu = $crateMenu;
	}

	/**
	 * @param Player            $player
	 * @param BaseFakeInventory $inventory
	 */
	public function closeInventory(Player $player, BaseFakeInventory $inventory){
		if((!is_null($this->getHandler())) && (!$this->getHandler()->isCancelled())){
			$chestBlock = $this->chestBlock;
			$typeBlock = $chestBlock->getLevel()->getBlock($chestBlock->subtract(0, 1));
			$type = $this->plugin->isCrateBlock($typeBlock->getId(), $typeBlock->getDamage());
			$reward = $this->getReward();

			if($player->isOnline()){
				$this->rewardPlayer($player, $reward, $type);
			}

			$this->getHandler()->cancel();
		}
	}

	/**
	 * @param Player $player
	 * @param Item   $item
	 * @param string $type
	 */
	public function rewardPlayer(Player $player, Item $item, string $type){
		if($item->getDamage() === $this->plugin->getConfig()->get("commandMeta")){
			$nbt = $item->getNamedTag();
			for($i = 0; $i < $this->plugin->getConfig()->get("maxCommands"); $i++){
				if($nbt->hasTag((string) $i, StringTag::class)){
					$cmd = $nbt->getString((string) $i);
					$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
				}
			}
		}else{
			$player->getInventory()->addItem($item);
			$win_message = str_replace(["%REWARD%", "%COUNT%", "%CRATE%"], [
				$item->getName(),
				$item->getCount(),
				ucfirst($type)
			], Lang::$win_message);
			$player->sendMessage($win_message);
		}
	}

	/**
	 * @return Item
	 */
	public function getReward(): Item{
		$chestBlock = $this->chestBlock;
		$player = $this->player;

		$typeBlock = $chestBlock->getLevel()->getBlock($chestBlock->subtract(0, 1));
		$type = $this->plugin->isCrateBlock($typeBlock->getId(), $typeBlock->getDamage());
		$drops = $this->plugin->getCrateDrops($type);

		$reward = array_rand($drops, 1);
		$reward = $drops[$reward];

		if(!isset($reward["id"]) || !isset($reward["meta"]) || !isset($reward["amount"])){
			$this->player->kick("§cMysteryCrate caught fire!\nPlease report to Admin to look for error on console.", false);
			$this->plugin->getLogger()->error("Either `id` or `meta` or `amount` key is missing in " . ucfirst($type) . " Crate.");
			$this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
		}

		$item = Item::get($reward["id"], $reward["meta"], $reward["amount"]);

		if(isset($reward["name"])){
			$item->setCustomName($reward["name"]);
		}
		if(isset($reward["lore"])){
			$item->setLore([$reward["lore"]]);
		}
		if(isset($reward["commands"])){
			foreach($reward["commands"] as $index => $cmd){
				$nbt = $item->getNamedTag() ?? new CompoundTag("", []);
				$cmd = str_replace(["%PLAYER%"], [$player->getName()], $cmd);
				$nbt->setString((string) $index, $cmd);
				$item->setNamedTag($nbt);
			}
		}
		if(isset($reward["enchantments"])){
			foreach($reward["enchantments"] as $enchantName => $enchantData){
				$level = $enchantData["level"];
				$ce = $this->plugin->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
				if(!is_null($enchant = Enchantment::getEnchantmentByName($enchantName)) || (!is_null($ce) && $ce->isEnabled() && !is_null($enchant = CustomEnchantManager::getEnchantmentByName($enchantName)))){
					$item->addEnchantment(new EnchantmentInstance($enchant, $level));
				}
			}
		}

		return $item;
	}

	/**
	 * @param int $timer
	 */
	public function onRun(int $timer){
		$t_delay = $this->t_delay;
		$chestBlock = $this->chestBlock;
		$player = $this->player;

		$crateMenu = $this->crateMenu;
		$crateInventory = $crateMenu->getInventory();

		if(($player instanceof Player) && ($player->isOnline())){
			$this->t_delay--;

			if($t_delay >= 0){
				$i = 0;
				while($i < 27){
					if($i !== 4 && $i !== 10 && $i !== 11 && $i !== 12 && $i !== 13 && $i !== 14 && $i !== 15 && $i !== 16 && $i !== 22){
						$this->setItem($i, Item::get(Item::VINE));
					}
					$i++;
				}

				$this->setItem(4, Item::get(Item::END_ROD));
				$this->setItem(22, Item::get(Item::END_ROD));

				$chestBlock->getLevel()->addSound(new ClickSound($chestBlock), [$player]);

				$reward = $this->getReward();
				$this->setItem(10, $crateInventory->getItem(11));
				$this->setItem(11, $crateInventory->getItem(12));
				$this->setItem(12, $crateInventory->getItem(13));
				$this->setItem(13, $crateInventory->getItem(14));//reward
				$this->setItem(14, $crateInventory->getItem(15));
				$this->setItem(15, $crateInventory->getItem(16));
				$this->setItem(16, $reward);
			}

			if($t_delay == -1){
				$this->setItem(10, Item::get(Item::AIR));
				$this->setItem(11, Item::get(Item::AIR));
				$this->setItem(12, Item::get(Item::AIR));
				$this->setItem(14, Item::get(Item::AIR));
				$this->setItem(15, Item::get(Item::AIR));
				$this->setItem(16, Item::get(Item::AIR));

				$reward = $crateInventory->getItem(13);

				$typeBlock = $chestBlock->getLevel()->getBlock($chestBlock->subtract(0, 1));
				$type = $this->plugin->isCrateBlock($typeBlock->getId(), $typeBlock->getDamage());

				if($player->isOnline()){
					$this->rewardPlayer($player, $reward, $type);
				}

				$this->getHandler()->cancel();
			}
		}
	}

	/**
	 * @param      $index
	 * @param Item $item
	 */
	public function setItem($index, Item $item){
		$crateMenu = $this->crateMenu;
		$crateMenu->getInventory()->setItem($index, $item);
	}
}
