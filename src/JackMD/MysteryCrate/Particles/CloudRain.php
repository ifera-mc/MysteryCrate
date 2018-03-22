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

namespace JackMD\MysteryCrate\Particles;

use JackMD\MysteryCrate\Main;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\WaterDripParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class CloudRain extends PluginTask

{
    private $plugin;

    /**
     * ParticleTask constructor.
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

        $cx = $this->plugin->X + 0.5;
        $cy = $this->plugin->Y;
        $cz = $this->plugin->Z + 0.5;
        $cpos = new Vector3($cx, $cy, $cz);

        $time = 1;
        $pi = 3.14159;
        $time = $time + 0.1 / $pi;
        for ($i = 0; $i <= 2 * $pi; $i += $pi / 8) {
            $x = $time * cos($i);
            $y = exp(-0.1 * $time) * sin($time) + 1.5;
            $z = $time * sin($i);
            $level->addParticle(new ExplodeParticle($cpos->add($x, $y, $z)));
            $level->addParticle(new WaterDripParticle($cpos->add($x, $y, $z)));

        }
    }
}