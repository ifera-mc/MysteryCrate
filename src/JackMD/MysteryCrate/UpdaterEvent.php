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
    public $ids = array(7, 49, 466, 260, 322, 352, 364, 264, 310, 311, 312, 313, 266, 265, 264, 388, 57, 41, 276, 278);
    public $scheduler;
    public $main;
    public $level;
    public $plugin;
    public $item;
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
                        if($i != 4 && $i != 10 && $i != 11 && $i != 12 && $i != 13 && $i != 14 && $i != 15 && $i != 16 && $i != 22) {
                            $this->setItem($i, 106, 1);
                        }
                        $i++;
                    }

                    $this->setItem(4, 208, 1);
                    $this->setItem(22, 208, 1);

                    $block = $this->chest;

                    $block->getLevel()->addSound(new ClickSound($block), [$this->player]);

                    $this->setItem(10, $this->chest->getInventory()->getItem(11)->getId(), 1);
                    $this->setItem(11, $this->chest->getInventory()->getItem(12)->getId(), 1);
                    $this->setItem(12, $this->chest->getInventory()->getItem(13)->getId(), 1);
                    $this->setItem(13, $this->chest->getInventory()->getItem(14)->getId(), 1);//reward
                    $this->setItem(14, $this->chest->getInventory()->getItem(15)->getId(), 1);
                    $this->setItem(15, $this->chest->getInventory()->getItem(16)->getId(), 1);
                    $this->setItem(16, $this->ids[(int)rand(0, 18)], 1);
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
        if ($id === 260) {
            $item->setCount((int)rand(20, 64));
        }
        if ($id === 7) {
            $item->setCount((int)rand(5, 15));
        }
        if ($id === 49) {
            $item->setCount((int)rand(5, 30));
        }
        if ($id === 466) {
            $item->setCount((int)rand(1, 8));
        }
        if ($id === 7) {
            $item->setCount((int)rand(5, 15));
        }
        if ($id === 322 || $id == 266 || $id == 265 || $id == 264) {
            $item->setCount((int)rand(5, 15));
        }
        if ($id === 352) {
            $item->setCount((int)rand(35, 64));
        }
        if ($id === 388) {
            $item->setCount(1);
        }
        if ($id === 364) {
            $item->setCount((int)rand(25, 64));
        }
        if ($item->getId() === 106) {
            if ((int)rand(1, 20) == 2) {
                $enchant = Enchantment::getEnchantment(0);
                $item->addEnchantment(new EnchantmentInstance($enchant, 1));
            }
        }
        if ($id === 276 || $id === 310 || $id === 311 || $id === 312 || $id === 313 || $id === 278 || $id === 57 || $id === 41) {
            $item->setCount(1);
            if ($id === 278 && (int)rand(1, 3) == 1) {
                $ef = Enchantment::getEnchantment(15);
                $item->addEnchantment(new EnchantmentInstance($ef, ((int)rand(3, 5))));
                if ((int)rand(1, 7) == 3) {
                    $dura = Enchantment::getEnchantment(17);
                    $item->addEnchantment(new EnchantmentInstance($dura, 4));
                }
                if ((int)rand(1, 5) == 3) {
                    $for = Enchantment::getEnchantment(18);
                    $item->addEnchantment(new EnchantmentInstance($for, ((int)rand(2, 4))));
                }
            }
            if ($id === 276 && (int)rand(1, 3) == 2) {
                $sharp = Enchantment::getEnchantment(9);
                $item->addEnchantment(new EnchantmentInstance($sharp, ((int)rand(2, 6))));
                if ((int)rand(1, 2) == 1) {
                    $knock = Enchantment::getEnchantment(12);
                    $item->addEnchantment(new EnchantmentInstance($knock, ((int)rand(1, 3))));
                }
                if ((int)rand(1, 3) == 2) {
                    $dura = Enchantment::getEnchantment(17);
                    $item->addEnchantment(new EnchantmentInstance($dura, ((int)rand(2, 4))));
                }
            }
            foreach (array(310, 311, 312, 313) as $armors) {
                if ($id == $armors) {
                    $protect = Enchantment::getEnchantment(0);
                    $item->addEnchantment(new EnchantmentInstance($protect, ((int)rand(3, 6))));
                }
            }
        }
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

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;
    }

}
