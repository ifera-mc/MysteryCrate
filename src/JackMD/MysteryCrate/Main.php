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
use pocketmine\block\Block;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
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
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

/**
 * Class Main
 * @package MysteryCrate
 */
class Main extends PluginBase implements Listener
{
    public $notInUse = true;
    public $task;
    public $crateName;
    public $crateHover;
    public $keyName;
    public $descOne;
    public $descTwo;
    public $crateWorld;
    public $X;
    public $Y;
    public $Z;

    public $crates;
    public $crateItems;
    public $crateBlocks;

    private $textParticle;
    private $cX;
    private $cY;
    private $cZ;

    public function onLoad()
    {
        $this->getLogger()->info(TextFormat::YELLOW . base64_decode("TXlzdGVyeUNyYXRlIGlzIGxvYWRpbmcuLi4="));
        $this->getLogger()->info(TextFormat::YELLOW . base64_decode("TWFrZSBzdXJlIHlvdSBoYXZlIFZhbmlsbGFFbmNoYW50bWVudHMgcGx1Z2luLg=="));
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(($this), $this);

        if (!is_dir($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }

        if ($this->getServer()->getPluginManager()->getPlugin(base64_decode("VmFuaWxsYUVuY2hhbnRtZW50cw=="))) {

            $this->initCrates();
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

            if ($this->getConfig()->getNested("ShowParticle") !== false) {
                if ($this->getServer()->getLevelByName($this->crateWorld) !== NULL) {
                    $this->getServer()->getScheduler()->scheduleRepeatingTask(new CloudRain($this), 5);

                } else {
                    $this->getServer()->getLogger()->critical(base64_decode("UGxlYXNlIHNldCB0aGUgY3JhdGVXb3JsZCBhbmQgWCwgWSwgWiBjb29yZGluYXRlcyBpbiB0aGUgY29uZmlnLnltbA=="));
                }
            }

            $this->getLogger()->info(TextFormat::GREEN . base64_decode("Cl9fXyAgX19fICAgICAgICAgIF8gICAgICAgICAgICAgICAgICBfX19fXyAgICAgICAgICAgXyAgICAgICAKfCAgXC8gIHwgICAgICAgICB8IHwgICAgICAgICAgICAgICAgLyAgX18gXCAgICAgICAgIHwgfCAgICAgIAp8IC4gIC4gfF8gICBfIF9fX3wgfF8gX19fIF8gX18gXyAgIF98IC8gIFwvXyBfXyBfXyBffCB8XyBfX18gCnwgfFwvfCB8IHwgfCAvIF9ffCBfXy8gXyB8ICdfX3wgfCB8IHwgfCAgIHwgJ19fLyBfYCB8IF9fLyBfIFwKfCB8ICB8IHwgfF98IFxfXyB8IHx8ICBfX3wgfCAgfCB8X3wgfCBcX18vfCB8IHwgKF98IHwgfHwgIF9fLwpcX3wgIHxfL1xfXywgfF9fXy9cX19cX19ffF98ICAgXF9fLCB8XF9fX198X3wgIFxfXyxffFxfX1xfX198CiAgICAgICAgIF9fLyB8ICAgICAgICAgICAgICAgICAgX18vIHwgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICB8X19fLyAgICAgICAgICAgICAgICAgIHxfX18vCiAgICAgICAgCkVuYWJsZWQgTXlzdGVyeUNyYXRlIGJ5IEphY2tNRCBmb3IgUG9ja2V0TWluZS1NUHMtQVBJCiAgICAgICAg"));
        } else {
            $this->getLogger()->error(base64_decode("VmFuaWxsYUVuY2hhbnRtZW50cyBwbHVnaW4gbm90IGZvdW5kLg=="));
            $this->getLogger()->error(base64_decode("VGhpcyBwbHVnaW4gZGVwZW5kcyBvbiBpdCB0byBhZGQgZW5jaGFudHMgb24gaXRlbXMu"));
            $this->getLogger()->error(base64_decode("VGhpcyB3aWxsIGNvbnRpbnVlIHVudGlsIFBvY2tldE1pbmUtTVAgcmVnaXN0ZXJzIGVuY2hhbnRzLg=="));
            $this->getPluginLoader()->disablePlugin($this);
        }
    }

    public function onDisable()
    {
        $this->getLogger()->info(TextFormat::RED . base64_decode("TXlzdGVyeUNyYXRlIERpc2FibGVkIQ=="));
    }

    public function initCrates()
    {
        $this->saveResource("items.yml");
        $file = new Config($this->getDataFolder() . "items.yml");
        foreach ($file->getNested("CrateItems") as $type => $values) {
            $this->crates[$type] = $values;
            $this->crateItems[$type] = $values["Items"];
            $this->crateBlocks[$values["block"]] = $type;
        }
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function getCrateItems(string $type)
    {
        return $this->crateItems[$type];
    }

    /**
     * @param int $id
     * @param int $meta
     * @return bool
     */
    public function isCrateBlock(int $id, int $meta)
    {
        return isset($this->crateBlocks[$id . ":" . $meta]) ? $this->crateBlocks[$id . ":" . $meta] : false;
    }

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
     * @param Item $item
     * @return bool
     */
    public function isCrateKey(Item $item)
    {
        $key = $item->getId() === 339 && $item->getDamage() === 50 && $item->getCustomName() === TextFormat::BOLD . TextFormat::GOLD . $this->keyName . TextFormat::RED . " Key" . TextFormat::RESET && $item->hasEnchantment(255, 1);
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

                                $chest->getInventory()->clearAll();

                                $this->task->chest = $chest;
                                $chest->setName(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . $this->crateName);
                                $this->task->player = $event->getPlayer();
                                $this->task->t_delay = 2 * 20;

                                $this->task->setCanTakeItem(false);

                                $item = $player->getInventory()->getItemInHand();
                                $item->setCount($item->getCount() - 1);
                                $item->setDamage($item->getDamage());
                                $event->getPlayer()->getInventory()->setItemInHand($item);
                                $this->task->scheduler = $this->getServer()->getScheduler();
                                $this->getServer()->getScheduler()->scheduleRepeatingTask($this->task, 5);

                                //Particle upon opening chest
                                $cx = $this->X + 0.5;
                                $cy = $this->Y + 1.2;
                                $cz = $this->Z + 0.5;
                                $radius = (int)1;
                                for ($i = 0; $i < 361; $i += 1.1) {
                                    $x = $cx + ($radius * cos($i));
                                    $z = $cz + ($radius * sin($i));
                                    $pos = new Vector3($x, $cy, $z);
                                    $block->level->addParticle(new LavaParticle($pos));
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

    /**
     * @return bool
     */
    public function isNotInUse(): bool
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
     * @param InventoryCloseEvent $event
     */
    public function onInventoryClose(InventoryCloseEvent $event)
    {
        if ($event->getInventory() instanceof ChestInventory) {
            $cpos = new Vector3((int)$this->X, (int)$this->Y, (int)$this->Z);
            $chestTile = $event->getPlayer()->getLevel()->getTile($cpos);

            if ($chestTile instanceof Chest) {
                $this->setNotInUse(true);
                $this->getServer()->getScheduler()->cancelTask($this->task->getTaskId());
            }
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
     * Adds custom FloatingText
     */
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

    /**
     * @param PlayerJoinEvent $ev
     */
    public function PlayerJoinEvent(PlayerJoinEvent $ev)
    {
        $lev = $ev->getPlayer()->getLevel();
        $crateLevel = $this->crateWorld;
        if ($lev->getFolderName() == $crateLevel) {
            $lev->addParticle($this->textParticle, [$ev->getPlayer()]);
        }

    }

    /**
     * @param EntityLevelChangeEvent $event
     */
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
