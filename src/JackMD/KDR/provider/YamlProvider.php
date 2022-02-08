<?php
declare(strict_types = 1);

/**
 *   _   _____________
 *  | | / /  _  \ ___ \
 *  | |/ /| | | | |_/ /
 *  |    \| | | |    /
 *  | |\  \ |/ /| |\ \
 *  \_| \_/___/ \_| \_|
 *
 * KDR, a Kill Death Ratio plugin for PocketMine-MP
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
 * KDR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License v3.0 for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see
 * <https://opensource.org/licenses/GPL-3.0>.
 * ------------------------------------------------------------------------
 */

namespace JackMD\KDR\provider;

use JackMD\KDR\KDR;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class YamlProvider implements ProviderInterface{
	
	public function prepare(): void{
		if(!is_dir(KDR::getInstance()->getDataFolder() . "data/")){
			mkdir(KDR::getInstance()->getDataFolder() . "data/");
		}
	}
	
	/**
	 * @param Player $player
	 */
	public function registerPlayer(Player $player): void{
		$config = new Config(KDR::getInstance()->getDataFolder() . "data/" . $player->getDisplayName() . ".yml", Config::YAML);
		if((!$config->exists("kills")) && (!$config->exists("deaths"))){
			$config->setAll(["kills" => 0, "deaths" => 0]);
			$config->save();
		}
	}
	
	/**
	 * @param Player $player
	 * @param int    $points
	 */
	public function addDeathPoints(Player $player, int $points = 1): void{
		$config = new Config(KDR::getInstance()->getDataFolder() . "data/" . $player->getDisplayName() . ".yml", Config::YAML);
		$config->set("deaths", $this->getPlayerDeathPoints($player) + $points);
		$config->save();
	}
	
	/**
	 * @param Player $player
	 * @return int
	 */
	public function getPlayerDeathPoints(Player $player): int{
		$config = new Config(KDR::getInstance()->getDataFolder() . "data/" . $player->getDisplayName() . ".yml", Config::YAML);
		return (int) $config->get("deaths");
	}
	
	/**
	 * @param Player $player
	 * @param int    $points
	 */
	public function addKillPoints(Player $player, int $points = 1): void{
		$config = new Config(KDR::getInstance()->getDataFolder() . "data/" . $player->getDisplayName() . ".yml", Config::YAML);
		$config->set("kills", $this->getPlayerKillPoints($player) + $points);
		$config->save();
	}
	
	/**
	 * @param Player $player
	 * @return bool
	 */
	public function playerExists(Player $player): bool{
		$config = new Config(KDR::getInstance()->getDataFolder() . "data/" . $player->getDisplayName() . ".yml", Config::YAML);
		return (($config->exists("kills")) && ($config->exists("deaths"))) ? true : false;
	}
	
	/**
	 * @param Player $player
	 * @return string
	 */
	public function getKillToDeathRatio(Player $player): string{
		$kills = $this->getPlayerKillPoints($player);
		$deaths = $this->getPlayerDeathPoints($player);
		if($deaths !== 0){
			$ratio = $kills / $deaths;
			if($ratio !== 0){
				return number_format($ratio, 1);
			}
		}
		return "0.0";
	}
	
	/**
	 * @param Player $player
	 * @return int
	 */
	public function getPlayerKillPoints(Player $player): int{
		$config = new Config(KDR::getInstance()->getDataFolder() . "data/" . $player->getDisplayName() . ".yml", Config::YAML);
		return (int) $config->get("kills");
	}
	
	public function close(): void{
		//useless in this case...
	}
}
