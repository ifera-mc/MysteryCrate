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
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class ParticleTask extends PluginTask

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
    public function onRun(int $tick) {

        $level = $this->plugin->getServer()->getLevelByName($this->plugin->crateWorld);

        $x = $this->plugin->X;
        $y = $this->plugin->Y;
        $z = $this->plugin->Z;

        $x1 = $this->plugin->X + 0.2;
        $y1 = $this->plugin->Y + 0.2;
        $z1 = $this->plugin->Z - 0.2;

        $x2 = $this->plugin->X + 0.4;
        $y2 = $this->plugin->Y + 0.4;
        $z2 = $this->plugin->Z - 0.4;

        $x3 = $this->plugin->X + 0.6;
        $y3 = $this->plugin->Y + 0.6;
        $z3 = $this->plugin->Z - 0.6;

        $particle = new FlameParticle(new Vector3($x, $y, $z));
        $level->addParticle($particle);
        $particle1 = new FlameParticle(new Vector3($x1, $y1, $z1));
        $level->addParticle($particle1);
        $particle2 = new FlameParticle(new Vector3($x2, $y2, $z2));
        $level->addParticle($particle2);
        $particle3 = new FlameParticle(new Vector3($x3, $y3, $z3));
        $level->addParticle($particle3);
        //TODO Finish particle work.
    }
}