<?php
declare(strict_types=1);

/**
 * ___  ___          _                  _____           _
 * |  \/  |         | |                /  __ \         | |
 * | .  . |_   _ ___| |_ ___ _ __ _   _| /  \/_ __ __ _| |_ ___
 * | |\/| | | | / __| __/ _ \ '__| | | | |   | '__/ _` | __/ _ \
 * | |  | | |_| \__ \ ||  __/ |  | |_| | \__/\ | | (_| | ||  __/
 * \_|  |_/\__, |___/\__\___|_|   \__, |\____/_|  \__,_|\__\___|
 *          __/ |                  __/ |
 *         |___/                  |___/  By @JackMD for PMMP
 *
 * MysteryCrate, a Crate plugin for PocketMine-MP
 * Copyright (c) 2018 JackMD  < https://github.com/JackMD >
 *
 * Discord: JackMD#3717
 * Twitter: JackMTaylor_
 *
 * This software is distributed under "GNU General Public License v3.0".
 * This license allows you to use it and/or modify it but you are not at
 * all allowed to sell this plugin at any cost. If found doing so the
 * necessary action required would be taken.
 *
 * MysteryCrate is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate\particle;

use JackMD\MysteryCrate\Main;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class DoubleHelix extends Task{

	/** @var Main */
	private $plugin;
	/** @var Vector3 */
	private $pos;

	/**
	 * DoubleHelix constructor.
	 *
	 * @param Main    $plugin
	 * @param Vector3 $pos
	 */
	public function __construct(Main $plugin, Vector3 $pos){
		$this->plugin = $plugin;
		$this->pos = $pos;
	}

	/**
	 * @param int $tick
	 */
	public function onRun(int $tick){
		$level = $this->plugin->getServer()->getLevelByName((string) $this->plugin->getConfig()->get("crateWorld"));
		$cpos = $this->pos;
		for($i = 5; $i > 0; $i -= 0.1){
			$radio = $i / 3;
			$x = $radio * cos(3 * $i);
			$y = 5 - $i;
			$z = $radio * sin(3 * $i);
			$level->addParticle(new FlameParticle($cpos->add($x, $y, $z)));
		}
		for($i = 5; $i > 0; $i -= 0.1){
			$radio = $i / 3;
			$x = -$radio * cos(3 * $i);
			$y = 5 - $i;
			$z = -$radio * sin(3 * $i);
			$level->addParticle(new FlameParticle($cpos->add($x, $y, $z)));
		}
	}
	
}