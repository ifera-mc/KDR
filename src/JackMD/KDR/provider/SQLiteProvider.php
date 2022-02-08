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

class SQLiteProvider implements ProviderInterface{
	
	/** @var \SQLite3 */
	public $killCounterDB;
	
	public function prepare(): void{
		$this->killCounterDB = new \SQLite3(KDR::getInstance()->getDataFolder() . "killCounter.db");
		$this->killCounterDB->exec("CREATE TABLE IF NOT EXISTS master (player TEXT PRIMARY KEY COLLATE NOCASE, kills INT, deaths INT)");
	}
	
	/**
	 * @param Player $player
	 */
	public function registerPlayer(Player $player): void{
		$stmt = $this->killCounterDB->prepare("INSERT OR REPLACE INTO master (player, kills, deaths) VALUES (:player, :kills, :deaths)");
		$stmt->bindValue(":player", $player->getLowerCaseName());
		$stmt->bindValue(":kills", "0");
		$stmt->bindValue(":deaths", "0");
		$stmt->execute();
	}
	
	/**
	 * @param Player $player
	 * @param int    $points
	 */
	public function addDeathPoints(Player $player, int $points = 1): void{
		$stmt = $this->killCounterDB->prepare("INSERT OR REPLACE INTO master (player, kills, deaths) VALUES (:player, :kills, :deaths)");
		$stmt->bindValue(":player", $player->getLowerCaseName());
		$stmt->bindValue(":kills", $this->getPlayerKillPoints($player));
		$stmt->bindValue(":deaths", $this->getPlayerDeathPoints($player) + $points);
		$stmt->execute();
	}
	
	/**
	 * @param Player $player
	 * @param int    $points
	 */
	public function addKillPoints(Player $player, int $points = 1): void{
		$stmt = $this->killCounterDB->prepare("INSERT OR REPLACE INTO master (player, kills, deaths) VALUES (:player, :kills, :deaths)");
		$stmt->bindValue(":player", $player->getLowerCaseName());
		$stmt->bindValue(":kills", $this->getPlayerKillPoints($player) + $points);
		$stmt->bindValue(":deaths", $this->getPlayerDeathPoints($player));
		$stmt->execute();
	}
	
	/**
	 * @param Player $player
	 * @return bool
	 */
	public function playerExists(Player $player): bool{
		$playerName = $player->getLowerCaseName();
		$result = $this->killCounterDB->query("SELECT player FROM master WHERE player='$playerName';");
		$array = $result->fetchArray(SQLITE3_ASSOC);
		return empty($array) == false;
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
		$playerName = $player->getLowerCaseName();
		$result = $this->killCounterDB->query("SELECT kills FROM master WHERE player = '$playerName'");
		$resultArray = $result->fetchArray(SQLITE3_ASSOC);
		return (int) $resultArray["kills"];
	}
	
	/**
	 * @param Player $player
	 * @return int
	 */
	public function getPlayerDeathPoints(Player $player): int{
		$playerName = $player->getLowerCaseName();
		$result = $this->killCounterDB->query("SELECT deaths FROM master WHERE player = '$playerName'");
		$resultArray = $result->fetchArray(SQLITE3_ASSOC);
		return (int) $resultArray["deaths"];
	}
	
	public function close(): void{
		$this->killCounterDB->close();
	}
}
