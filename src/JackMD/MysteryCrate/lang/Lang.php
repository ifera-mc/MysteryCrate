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
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\MysteryCrate\lang;

use JackMD\MysteryCrate\Main;
use pocketmine\utils\Config;

class Lang{

	/** @var string */
	public static $no_perm_destroy;
	/** @var string */
	public static $no_perm_create;
	/** @var string */
	public static $crate_destroy_successful;
	/** @var string */
	public static $crate_place_successful;
	/** @var string */
	public static $no_perm_use_crate;
	/** @var string */
	public static $no_key;
	/** @var string */
	public static $error_sneak;
	/** @var string */
	public static $error_crate_in_use;
	/** @var string */
	public static $win_message;

	/**
	 * @param Main $plugin
	 */
	public static function init(Main $plugin){
		$plugin->saveResource("lang.yml");
		$lang = new Config($plugin->getDataFolder() . "lang.yml", Config::YAML);
		self::loadMessages($lang);
	}

	/**
	 * @param Config $lang
	 */
	private static function loadMessages(Config $lang){
		self::$no_perm_destroy = $lang->get("no_perm_destroy");
		self::$no_perm_create = $lang->get("no_perm_create");
		self::$crate_destroy_successful = $lang->get("crate_destroy_successful");
		self::$crate_place_successful = $lang->get("crate_place_successful");
		self::$no_perm_use_crate = $lang->get("no_perm_use_crate");
		self::$no_key = $lang->get("no_key");
		self::$error_sneak = $lang->get("error_sneak");
		self::$error_crate_in_use = $lang->get("error_crate_in_use");
		self::$win_message = $lang->get("win_message");
	}
}
