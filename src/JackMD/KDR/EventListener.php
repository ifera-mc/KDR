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

namespace JackMD\KDR;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class EventListener implements Listener{

	/** @var KDR */
	public $plugin;
	
	/**
	 * EventListener constructor.
	 *
	 * @param KDR $plugin
	 */
	public function __construct(KDR $plugin){
		$this->plugin = $plugin;
	}
	
	/**
	 * @param PlayerJoinEvent $event
	 */
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if(!$this->plugin->getProvider()->playerExists($player)){
			$this->plugin->getProvider()->registerPlayer($player);
		}
	}
	
	/**
	 * @param PlayerDeathEvent $event
	 */
	public function onPlayerKill(PlayerDeathEvent $event){
		$player = $event->getPlayer();
		if($player instanceof Player){
			$this->plugin->getProvider()->addDeathPoints($player, (int) $this->plugin->getConfig()->get("death-points"));
		}
		$cause = $player->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$this->plugin->getProvider()->addKillPoints($damager, (int) $this->plugin->getConfig()->get("kill-points"));
			}
		}
	}
}
