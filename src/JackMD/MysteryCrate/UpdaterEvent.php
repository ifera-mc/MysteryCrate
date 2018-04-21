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

use JackMD\MysteryCrate\Task\PutChest;
use JackMD\MysteryCrate\Task\RemoveChest;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Chest;
use pocketmine\utils\TextFormat;

/**
 * @property  main
 */
class UpdaterEvent extends PluginTask
{
    public $canTakeItem = false;
    public $t_delay = 2 * 20;
    public $scheduler;
    public $plugin;
    public $player;

    /** @var Chest */
    public $chest;

    /**
     * UpdaterEvent constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    /**
     * @param int $timer
     */
    public function onRun(int $timer)
    {
        if ($this->chest instanceof Chest != NULL) {
            $this->t_delay--;
            if ($this->t_delay >= 0) {
                if ($this->chest instanceof Chest) {

                    $i = 0;
                    while ($i < 27) {
                        if ($i != 4 && $i != 10 && $i != 11 && $i != 12 && $i != 13 && $i != 14 && $i != 15 && $i != 16 && $i != 22) {
                            $this->setItem($i, 106, 1);
                        }
                        $i++;
                    }

                    $this->setItem(4, 208, 1);
                    $this->setItem(22, 208, 1);

                    $block = $this->chest;
                    $block->getLevel()->addSound(new ClickSound($block), [$this->player]);

                    $level = $this->plugin->getServer()->getLevelByName($this->plugin->crateWorld);
                    $cx = $this->plugin->X;
                    $cy = $this->plugin->Y;
                    $cz = $this->plugin->Z;
                    $cpos = new Vector3($cx, $cy, $cz);
                    $b = $level->getBlock($cpos);
                    $type = $this->plugin->isCrateBlock($b->getId(), $b->getDamage());
                    $drops = array_rand($this->plugin->getCrateItems($type), 1);
                    if (!is_array($drops)) {
                        $drops = [$drops];
                    }
                    foreach ($drops as $drop) {
                        $values = $this->plugin->getCrateItems($type)[$drop];
                        $i = Item::get($drop, $values["meta"], $values["amount"]);
                        if (isset($values["enchantments"])) {
                            foreach ($values["enchantments"] as $enchantment => $enchantmentinfo) {
                                $level = $enchantmentinfo["level"];
                                if (!is_null($ce = $this->plugin->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants")) && !is_null($enchant = \PiggyCustomEnchants\CustomEnchants\CustomEnchants::getEnchantmentByName($enchantment))) {
                                    $i = $ce->addEnchantment($i, $enchantment, $level);
                                } else {
                                    if (!is_null($enchant = Enchantment::getEnchantmentByName($enchantment))) {
                                        $i->addEnchantment(new EnchantmentInstance($enchant, $level));
                                    }
                                }
                            }
                        }
                        $i->setCustomName($values["name"]);

                        $cInv = $this->chest->getInventory();

                        $this->setItemS(10, $this->chest->getInventory()->getItem(11), $cInv->getItem(11)->getCount());
                        $this->setItemS(11, $this->chest->getInventory()->getItem(12), $cInv->getItem(12)->getCount());
                        $this->setItemS(12, $this->chest->getInventory()->getItem(13), $cInv->getItem(13)->getCount());
                        $this->setItemS(13, $this->chest->getInventory()->getItem(14), $cInv->getItem(14)->getCount());//reward
                        $this->setItemS(14, $this->chest->getInventory()->getItem(15), $cInv->getItem(15)->getCount());
                        $this->setItemS(15, $this->chest->getInventory()->getItem(16), $cInv->getItem(16)->getCount());
                        $this->setItemS(16, $i, $i->getCount());
                    }
                }
            }
            if ($this->t_delay == -1) {
                if ($this->chest instanceof Chest) {

                    $this->setItem(10, 0, 0);
                    $this->setItem(11, 0, 0);
                    $this->setItem(12, 0, 0);
                    $this->setItem(14, 0, 0);
                    $this->setItem(15, 0, 0);
                    $this->setItem(16, 0, 0);

                    $this->setCanTakeItem(true);

                    $this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
                    $cpos = new Vector3((int)$this->plugin->X, (int)$this->plugin->Y, (int)$this->plugin->Z);

                    $slot13 = $this->chest->getInventory()->getItem(13);

                    if ($this->player instanceof Player) {
                        $this->player->getInventory()->addItem($slot13);
                        $this->player->sendMessage(TextFormat::GREEN . "You recieved " . TextFormat::YELLOW . $slot13->getName() . TextFormat::LIGHT_PURPLE . " (x" . $slot13->getCount() . ")" . TextFormat::GREEN . " from " . $this->plugin->crateName);
                    }

                    $this->plugin->getServer()->getScheduler()->scheduleDelayedTask(new RemoveChest($this->plugin, $cpos), 20);
                    $this->plugin->getServer()->getScheduler()->scheduleDelayedTask(new PutChest($this->plugin, $cpos), 24);

                }
            }
        }
    }

    /**
     * @param $index
     * @param int $id
     * @param $count
     * @param int $dmg
     */
    public function setItem($index, int $id, $count, $dmg = 0)
    {
        $item = Item::get($id);
        $item->setCount($count);
        $item->setDamage($dmg);
        if ($this->chest instanceof Chest) {
            $this->chest->getInventory()->setItem($index, $item);
        }
    }

    /**
     * @param $index
     * @param Item $item
     * @param $count
     * @param int $dmg
     */
    public function setItemS($index, Item $item, $count, $dmg = 0)
    {
        $item->setCount($count);
        $item->setDamage($dmg);
        if ($this->chest instanceof Chest) {
            $this->chest->getInventory()->setItem($index, $item);
        }
    }

    /**
     * @return bool
     */
    public function isCanTakeItem(): bool
    {
        return $this->canTakeItem;
    }

    /**
     * @param bool $canTakeItem
     */
    public function setCanTakeItem(bool $canTakeItem)
    {
        $this->canTakeItem = $canTakeItem;
    }
}
