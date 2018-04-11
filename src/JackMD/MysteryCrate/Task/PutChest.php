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

namespace JackMD\MysteryCrate\Task;

use JackMD\MysteryCrate\Main;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class PutChest extends PluginTask
{
    private $plugin;
    public $chest;

    /**
     * PutChest constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    /**
     * @param int $tick
     */
    public function onRun(int $tick)
    {
        $level = $this->plugin->getServer()->getLevelByName($this->plugin->crateWorld);
        $cx = $this->plugin->X;
        $cy = $this->plugin->Y;
        $cz = $this->plugin->Z;
        $cpos = new Vector3($cx, $cy, $cz);

        $level->setBlock($cpos, Block::get(54));
        
        $this->plugin->setNotInUse(true);

    }
}
