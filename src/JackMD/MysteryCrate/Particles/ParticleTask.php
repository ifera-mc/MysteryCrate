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
use pocketmine\level\Level;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class ParticleTask extends PluginTask

{
    private $plugin;
    private $center;
    private $radius;
    private $level;
    private $particle;

    /**
     * ParticleTask constructor.
     * @param Main $plugin
     * @param $particle
     * @param $level
     * @param $radius
     * @param $center
     */
    public function __construct(Main $plugin, $particle, $level, $radius, $center)
    {
        parent::__construct($plugin);

        $this->plugin = $plugin;
        $this->level = $level;
        $this->radius = (float)$radius;
        $this->center = $center;
        $this->particle = $particle;
        $this->radius = $radius;
    }

    /**
     * @param int $tick
     */
    public function onRun(int $tick)
    {
        if ($this->level instanceof Level) {
            if ($this->particle instanceof Particle) {
                $radius = $this->radius;
                $y = $this->center->y;

                for ($i = 0; $i < 361; $i += 1.1) {
                    $x = $this->center->x + ($radius * cos($i));
                    $z = $this->center->z + ($radius * sin($i));
                    $pos = new Vector3($x, $y, $z);
                    $this->level->addParticle(new LavaParticle($pos));
                }
            }
        }
    }
}
