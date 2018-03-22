<?php

namespace JackMD\MysteryCrate\Task;

use JackMD\MysteryCrate\Main;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class PutChest extends PluginTask
{
    private $plugin;
    public $chest;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun(int $tick)
    {
        $level = $this->plugin->getServer()->getLevelByName($this->plugin->crateWorld);
        $cx = $this->plugin->X;
        $cy = $this->plugin->Y;
        $cz = $this->plugin->Z;
        $cpos = new Vector3($cx, $cy, $cz);

        $level->setBlock($cpos, Block::get(54));

    }
}
