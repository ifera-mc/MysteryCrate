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

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\sound\ClickSound;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Chest;

/**
 * @property  main
 */
class UpdaterEvent extends PluginTask
{
    public $canTakeItem = true;
    public $t_delay = 3 * 20;
    public $ids = array(7, 49, 466, 260, 322, 352, 364, 264, 310, 311, 312, 313, 266, 265, 264, 388, 57, 41, 276, 278);
    public $scheduler;
    public $main;
    public $level;
    public $plugin;
    public $item;
    public $player;
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

                    $this->setItem(0, 106, 1);
                    $this->setItem(1, 106, 1);
                    $this->setItem(2, 106, 1);
                    $this->setItem(3, 106, 1);

                    $this->setItem(4, 208, 1);

                    $this->setItem(5, 106, 1);
                    $this->setItem(6, 106, 1);
                    $this->setItem(7, 106, 1);
                    $this->setItem(8, 106, 1);
                    $this->setItem(9, 106, 1);
                    $this->setItem(17, 106, 1);
                    $this->setItem(18, 106, 1);
                    $this->setItem(19, 106, 1);
                    $this->setItem(20, 106, 1);
                    $this->setItem(21, 106, 1);

                    $this->setItem(22, 208, 1);

                    $this->setItem(23, 106, 1);
                    $this->setItem(24, 106, 1);
                    $this->setItem(25, 106, 1);
                    $this->setItem(26, 106, 1);

                    $block = $this->chest;

                    $block->getLevel()->addSound(new ClickSound($block));

                    $this->setItem(10, $this->ids[(int)rand(0, 18)], 1);
                    $this->setItem(11, $this->ids[(int)rand(0, 18)], 1);
                    $this->setItem(12, $this->ids[(int)rand(0, 18)], 1);
                    $this->setItem(13, $this->ids[(int)rand(0, 18)], 1);
                    $this->setItem(14, $this->ids[(int)rand(0, 18)], 1);
                    $this->setItem(15, $this->ids[(int)rand(0, 18)], 1);
                    $this->setItem(16, $this->ids[(int)rand(0, 18)], 1);
                }
            }
            if ($this->t_delay == -1) {
                if ($this->chest instanceof Chest) {

                    $this->setItem(0, 0, 1);
                    $this->setItem(1, 0, 1);
                    $this->setItem(2, 0, 1);
                    $this->setItem(3, 0, 1);
                    $this->setItem(4, 0, 1);
                    $this->setItem(5, 0, 1);
                    $this->setItem(6, 0, 1);
                    $this->setItem(7, 0, 1);
                    $this->setItem(8, 0, 1);
                    $this->setItem(9, 0, 0);
                    $this->setItem(10, 0, 0);
                    $this->setItem(11, 0, 0);
                    $this->setItem(12, 0, 0);

                    $this->setItem(14, 0, 0);
                    $this->setItem(15, 0, 1);
                    $this->setItem(16, 0, 1);
                    $this->setItem(17, 0, 1);
                    $this->setItem(18, 0, 1);
                    $this->setItem(19, 0, 1);
                    $this->setItem(20, 0, 1);
                    $this->setItem(21, 0, 1);
                    $this->setItem(22, 0, 1);
                    $this->setItem(23, 0, 1);
                    $this->setItem(24, 0, 1);
                    $this->setItem(25, 0, 1);
                    $this->setItem(26, 0, 1);
                    $this->canTakeItem = true;

                    $this->plugin->getServer()->getScheduler()->cancelTask($this->getTaskId());
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
     * @param int $t_delay
     * @return UpdaterEvent
     */
    public function setTDelay(int $t_delay): UpdaterEvent
    {
        $this->t_delay = $t_delay;
        return $this;
    }

    /**
     * @param array $ids
     * @return UpdaterEvent
     */
    public function setIds(array $ids): UpdaterEvent
    {
        $this->ids = $ids;
        return $this;
    }

}
