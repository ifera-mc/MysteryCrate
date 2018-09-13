<?php

namespace JackMD\MysteryCrate\Utils;

use JackMD\MysteryCrate\Main;
use pocketmine\utils\Config;

class Lang{
	
	public static $no_perm_destroy;
	public static $no_perm_create;
	public static $crate_destroy_successful;
	public static $crate_place_successful;
	public static $no_perm_use_crate;
	public static $no_key;
	public static $error_sneak;
	public static $error_crate_in_use;
	public static $win_message;
	
	public static function init(Main $plugin){
		$plugin->saveResource("lang.yml");
		$lang = new Config($plugin->getDataFolder() . "lang.yml", Config::YAML);
		self::loadMessages($lang);
	}
	
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
