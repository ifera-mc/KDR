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

use JackMD\KDR\provider\ProviderInterface;
use JackMD\KDR\provider\SQLiteProvider;
use JackMD\KDR\provider\YamlProvider;
use pocketmine\plugin\PluginBase;

class KDR extends PluginBase{
	
	/** @var KDR */
	private static $instance;
	
	/** @var ProviderInterface */
	private $provider;
	
	/**
	 * @return KDR
	 */
	public static function getInstance(): KDR{
		return self::$instance;
	}
	
	public function onLoad(): void{
		self::$instance = $this;
	}
	
	public function onEnable(): void{
		$this->saveDefaultConfig();
		$this->setProvider();
		$this->getProvider()->prepare();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getLogger()->info("KillCounter Plugin Enabled.");
	}
	
	/**
	 * @return bool
	 */
	private function isValidProvider(): bool{
		if(!isset($this->provider) || ($this->provider === null) || !($this->provider instanceof ProviderInterface)){
			return false;
		}
		return true;
	}
	
	public function onDisable(){
		if($this->isValidProvider()){
			$this->getProvider()->close();
		}
	}
	
	/**
	 * @return ProviderInterface
	 */
	public function getProvider(): ProviderInterface{
		return $this->provider;
	}
	
	private function setProvider(): void{
		$providerName = $this->getConfig()->get("data-provider");
		$provider = null;
		switch(strtolower($providerName)){
			case "sqlite":
				$provider = new SQLiteProvider();
				$this->getLogger()->notice("SQLiteProvider successfully enabled.");
				break;
			case "yaml":
				$provider = new YamlProvider();
				$this->getLogger()->notice("YamlProvider successfully enabled.");
				break;
			default:
				$this->getLogger()->error("Please set a valid data-provider in config.yml. Plugin Disabled");
				$this->getServer()->getPluginManager()->disablePlugin($this);
				break;
		}
		if($provider instanceof ProviderInterface){
			$this->provider = $provider;
		}
	}
}

