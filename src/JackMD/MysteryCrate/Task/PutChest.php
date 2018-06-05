<?php

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

namespace JackMD\MysteryCrate\Task;

use JackMD\MysteryCrate\Main;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class PutChest extends Task{
	
	private $plugin, $cpos, $dmg;
	
	/**
	 * PutChest constructor.
	 *
	 * @param Main    $plugin
	 * @param Vector3 $cpos
	 * @param int     $dmg
	 */
	public function __construct(Main $plugin, Vector3 $cpos, int $dmg){
		$this->plugin = $plugin;
		$this->cpos = $cpos;
		$this->dmg = $dmg;
	}
	
	/**
	 * @param int $tick
	 */
	public function onRun(int $tick){
		$level = $this->plugin->getServer()->getLevelByName($this->plugin->getConfig()->get("crateWorld"));
		$cpos = $this->cpos;
		$dmg = $this->dmg;
		$level->setBlock($cpos, Block::get(54, $dmg));
		$this->plugin->setNotInUse(true);
	}
}
