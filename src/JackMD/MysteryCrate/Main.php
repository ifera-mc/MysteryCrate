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

namespace JackMD\MysteryCrate;

use JackMD\MysteryCrate\Commands\KeyCommand;
use JackMD\MysteryCrate\Commands\xyzCommand;
use JackMD\MysteryCrate\Particles\CloudRain;
use JackMD\MysteryCrate\Particles\ParticleTask;
use pocketmine\block\Block;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Chest;
use pocketmine\utils\TextFormat;

/**
 * Class Main
 * @package MysteryCrate
 */
class Main extends PluginBase implements Listener
{

    private static $instance;

    public $notInUse = true;

    public $item;

    /** @var UpdaterEvent */
    public $task;

    /** @var ParticleTask */
    public $particle;

    public $crateName;
    public $crateHover;
    public $keyName;
    public $descOne;
    public $descTwo;
    public $crateWorld;
    public $X;
    public $Y;
    public $Z;

    public $textParticle;
    private $cX;
    private $cY;
    private $cZ;

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function onLoad()
    {
        $this->getLogger()->info(TextFormat::YELLOW . "MysteryCrate is loading...");
        $this->getLogger()->info(TextFormat::YELLOW . "Make sure you have VanillaEnchantment plugin.");
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(($this), $this);
        if (!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }

        if ($this->getServer()->getPluginManager()->getPlugin("VanillaEnchantments")) {

            $this->saveDefaultConfig();
            $this->crateName = $this->getConfig()->getNested("crateName");
            $this->crateHover = $this->getConfig()->getNested("crateHover");
            $this->keyName = $this->getConfig()->getNested("keyName");
            $this->descOne = $this->getConfig()->getNested("descOne");
            $this->descTwo = $this->getConfig()->getNested("descTwo");
            $this->crateWorld = $this->getConfig()->getNested("crateWorld");
            $this->X = $this->getConfig()->getNested("X");
            $this->Y = $this->getConfig()->getNested("Y");
            $this->Z = $this->getConfig()->getNested("Z");

            $this->getServer()->getCommandMap()->register("key", new KeyCommand("key", $this), "key");
            $this->getServer()->getCommandMap()->register("xyz", new xyzCommand("xyz", $this), "xyz");

            $this->task = new UpdaterEvent($this);

            $this->initTextParticle();

            $radius = 3;
            if ($radius > 0) {
                $v3 = new Vector3($this->X + 0.5, $this->Y + 2, $this->Z + 0.5);
                $this->getServer()->getScheduler()->scheduleRepeatingTask(new ParticleTask($this, $this->particle, $this->crateWorld, $radius, $v3), 5)->getTaskId();
            }

            if($this->getServer()->getLevelByName($this->crateWorld) !== NULL) {

            $taskCloud = new CloudRain($this);
            $this->getServer()->getScheduler()->scheduleRepeatingTask($taskCloud, 5);
            
            }else{
                $this->getServer()->getLogger()->critical("Please set the crateWorld and X, Y, Z coordinates in the config.yml");
            }

            $this->getLogger()->info(TextFormat::GREEN . "
___  ___          _                  _____           _       
|  \/  |         | |                /  __ \         | |      
| .  . |_   _ ___| |_ ___ _ __ _   _| /  \/_ __ __ _| |_ ___ 
| |\/| | | | / __| __/ _ | '__| | | | |   | '__/ _` | __/ _ \
| |  | | |_| \__ | ||  __| |  | |_| | \__/| | | (_| | ||  __/
\_|  |_/\__, |___/\__\___|_|   \__, |\____|_|  \__,_|\__\___|
         __/ |                  __/ |                        
        |___/                  |___/
        
Enabled MysteryCrate by JackMD for PocketMine-MPs-API
        ");
        } else {
            $this->getLogger()->error("VanillaEnchantments plugin not found.");
            $this->getLogger()->error("This plugin depends on it to add enchants on items.");
            $this->getLogger()->error("This will continue until PocketMine-MP registers enchants.");
            $this->onDisable();
        }
    }

    private function initTextParticle()
    {
        if (!$this->textParticle instanceof FloatingTextParticle) {

            $x = $this->X + 0.5;
            $y = $this->Y + 1;
            $z = $this->Z + 0.5;

            $pos = new Vector3($x, $y, $z);

            $this->textParticle = new FloatingTextParticle($pos, '', $this->crateHover . TextFormat::RESET);
        }
    }

    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::RED . "MysteryCrate Disabled!");
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        $amount = 1;
        return $amount;
    }

    public function getCrate(){}

    /**
     * @param Player $player
     * @param int $amount
     * @return bool
     */
    public function giveKey(Player $player, int $amount)
    {
        $key = $this->getKey($amount);

        $key->addEnchantment(new EnchantmentInstance(new Enchantment(255, "", Enchantment::RARITY_COMMON, Enchantment::SLOT_ALL, 1))); //Glowing key effect
        $key->setCustomName(TextFormat::BOLD . TextFormat::GOLD . $this->keyName . TextFormat::RED . " Key" . TextFormat::RESET);
        $key->setLore([$this->descOne, $this->descTwo,]);

        $player->getInventory()->addItem($key);

        return true;
    }

    /**
     * @param int $amount
     * @return Item
     */
    public function getKey(int $amount)
    {
        $key = Item::get(Item::PAPER, 50, $amount);
        return $key;
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {

        $player = $event->getPlayer();
        $heldItem = $player->getInventory()->getItemInHand();
        $block = $event->getBlock();
        $level = $block->getLevel()->getFolderName();
        $item = $event->getItem();
        $isKey = $this->isCrateKey($item);

        if ($block->getId() === 54) {

            $pos = new Vector3($block->x, $block->y, $block->z);
            $cpos = new Vector3((int)$this->X, (int)$this->Y, (int)$this->Z);

            if ($pos->equals($cpos)) {

                if ($level === $this->crateWorld) {

                    if ($heldItem == $isKey) {

                        $chest = $event->getPlayer()->getLevel()->getTile(new Vector3($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ()));
                        if ($chest instanceof Chest) {

                            if ($this->isNotInUse()) {
                                $this->setNotInUse(false);
                                $this->task->chest = $chest;
                                $chest->setName(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . $this->crateName);
                                $this->task->player = $event->getPlayer();
                                $this->task->t_delay = 3 * 20;

                                $this->task->setCanTakeItem(false);

                                $item = $player->getInventory()->getItemInHand();
                                $item->setCount($item->getCount() - 1);
                                $item->setDamage($item->getDamage());
                                $event->getPlayer()->getInventory()->setItemInHand($item);
                                $this->task->scheduler = $this->getServer()->getScheduler();
                                $this->getServer()->getScheduler()->scheduleRepeatingTask($this->task, 3);

                                $v3 = $cpos;
                                $v3->x += 0.5;
                                $v3->y += 1.2;
                                $v3->z += 0.5;
                                for ($i = 0; $i <= 15; $i++) {
                                    $scatter = 0.15;
                                    $vector3 = $v3;
                                    $vector3->x += $this->randomFloat(-$scatter, $scatter);
                                    $vector3->y += $this->randomFloat(-0.1, 0.1);
                                    $vector3->z += $this->randomFloat(-$scatter, $scatter);
                                    $block->level->addParticle(new LavaParticle($vector3));
                                }

                            } else {
                                $event->setCancelled();
                                $player->sendMessage(TextFormat::RED . "Crate is in use..");
                            }
                        }
                    } else {
                        $event->setCancelled();
                        $player->sendMessage(TextFormat::DARK_RED . "You need to be holding a " . TextFormat::LIGHT_PURPLE . $this->keyName . TextFormat::DARK_RED . " key to get " . $this->crateName . TextFormat::DARK_RED . " rewards!");
                    }
                }
            }
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isCrateKey(Item $item)
    {
        $key = $item->getId() === 339 && $item->getDamage() === 50 && $item->getCustomName() === TextFormat::BOLD . TextFormat::GOLD . $this->keyName . TextFormat::RED . " Key" . TextFormat::RESET && $item->hasEnchantment(255, 1);
        return $key;
    }

    /**
     * @return bool
     */
    public function isNotInUse(): bool
    {
        return $this->notInUse;
    }

    public function getNotInUse(bool $val)
    {
        if ($this->isNotInUse() !== $val) {
            return false;
        }
        return true;
    }

    /**
     * @param bool $notInUse
     */
    public function setNotInUse(bool $notInUse)
    {
        $this->notInUse = $notInUse;
    }

    private function randomFloat($min = -1.2, $max = 1.2)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * @param InventoryOpenEvent $event
     */
    public function onInventoryOpen(InventoryOpenEvent $event)
    {

        $cpos = new Vector3((int)$this->X, (int)$this->Y, (int)$this->Z);
        $chest = $event->getPlayer()->getLevel()->getBlock($cpos);
        $chestTile = $event->getPlayer()->getLevel()->getTile(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()));

        if ($chestTile instanceof Chest) {
            $chestInv = $event->getInventory()->getViewers();
            if ($chestInv !== []) {
                $event->setCancelled();
            }
        }

    }

    /**
     * @param InventoryCloseEvent $event
     */
    public function onInventoryClose(InventoryCloseEvent $event)
    {

        $cpos = new Vector3((int)$this->X, (int)$this->Y, (int)$this->Z);
        $chest = $event->getPlayer()->getLevel()->getBlock($cpos);
        $chestTile = $event->getPlayer()->getLevel()->getTile(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()));

        if ($chestTile instanceof Chest) {
            $chestInv = $event->getInventory()->getViewers();
            if ($chestInv == []) {
                $event->setCancelled();
            }
            $this->setNotInUse(true);
            $this->getServer()->getScheduler()->cancelTask($this->task->getTaskId());
        }
    }

    /**
     * @param InventoryTransactionEvent $event
     */
    public function onInventoryTransaction(InventoryTransactionEvent $event)
    {
        $cpos = new Vector3((int)$this->X, (int)$this->Y, (int)$this->Z);
        $chest = $event->getTransaction()->getSource()->getPlayer()->getLevel()->getBlock($cpos);
        $chestTile = $event->getTransaction()->getSource()->getLevel()->getTile(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()));
        if ($chestTile instanceof Chest) {
            foreach ($event->getTransaction()->getActions() as $action) {
                if ($action instanceof SlotChangeAction) {
                    $inventory = $action->getInventory();
                    if ($inventory instanceof ChestInventory) {
                        if ($this->task !== NULL && $this->task->isCanTakeItem()) {
                            $event->setCancelled(true);
                        } else {
                            $event->setCancelled(true);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onTouch(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $heldItem = $player->getInventory()->getItemInHand();
        $isXyzLocator = $this->isXyzLocator($heldItem);
        if ($heldItem == $isXyzLocator and $player->isOp() and $event->getBlock()->getID() != Block::AIR) {
            {
                $this->cX = $event->getBlock()->getX();
                $this->cY = $event->getBlock()->getY();
                $this->cZ = $event->getBlock()->getZ();
                $event->setCancelled(true);
            }
            $player->sendMessage(TextFormat::GREEN . "Block coordinates are " . TextFormat::YELLOW . "X " . TextFormat::LIGHT_PURPLE . $this->cX . TextFormat::YELLOW . " Y " . TextFormat::LIGHT_PURPLE . $this->cY . TextFormat::YELLOW . " Z " . TextFormat::LIGHT_PURPLE . $this->cZ);
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isXyzLocator(Item $item)
    {
        $key = $item->getId() === 286 && $item->getDamage() === 0 && $item->getCustomName() === TextFormat::BOLD . TextFormat::GOLD . "XYZ" . TextFormat::RED . " Locator" . TextFormat::RESET && $item->hasEnchantment(255, 1);
        return $key;
    }

    public function PlayerJoinEvent(PlayerJoinEvent $ev)
    {
        $lev = $ev->getPlayer()->getLevel();
        $crateLevel = $this->crateWorld;
        if ($lev->getFolderName() == $crateLevel) {
            $lev->addParticle($this->textParticle, [$ev->getPlayer()]);
        }

    }

    public function onLevelChange(EntityLevelChangeEvent $event)
    {

        $targetLevel = $event->getTarget();
        $crateLevel = $this->crateWorld;

        if ($event->getEntity() instanceof Player) {
            if ($this->textParticle instanceof FloatingTextParticle) {
                if ($targetLevel->getFolderName() == $crateLevel) {
                    $this->textParticle->setInvisible(false);
                    $lev = $event->getTarget();
                    $lev->addParticle($this->textParticle, [$event->getEntity()]);
                } else {
                    $this->textParticle->setInvisible(true);
                    $lev = $event->getOrigin();
                    $lev->addParticle($this->textParticle, [$event->getEntity()]);
                }
            }
        }
    }
}
