<?php

namespace ColorText;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class ColorText extends PluginBase implements Listener{
	private $coloredPlayers;
	public function onEnable(){
		@mkdir($this->getDataFolder()); //<-Create plugin directory
		$this->saveDefaultConfig(); //<-Save default config
		$this->getConfig()->getAll(); //<- Get config
		if($this->getConfig()->get("enabled") != true){
			$this->getLogger()->info("Plugin disabled");
			$this->getServer()->getPluginManager()->disablePlugin($this); // I confirmed this method is corect
			return;
		}else{
			$this->getServer()->getPluginManager()->registerEvents($this, $this);
			$this->coloredPlayers = new Config($this->getDataFolder() . "colored-players.txt", Config::ENUM);
		}
	}
	public function onDisable(){
		$this->coloredPlayers->save();
	}
	/**
	 * @priority HIGHEST
	 *           ^^^^^^^ Do not change anything outside your plugin scope at the MONITOR priority
	 * @ignoreCancelled true
	 */
	public function onJoin(PlayerJoinEvent $ev){
		if($this->coloredPlayers->exists($ev->getPlayer()->getName(), true)){ // check case-insensitively (the true parameter) if the player is in the colored players list
			$ev->getPlayer()->setRemoveFormat(true);
		}
	}
	/**
	 * @param PlayerChatEvent $event
	 *
	 * @priority LOWEST
	 * @ignoreCancelled false
	 */
	public function onChat(PlayerChatEvent $event){
		$sender = $event->getPlayer();
		$message = $event->getMessage();
		$event->setFormat($this->getConfig()->get("name") . "<%s>§d " . $this->getConfig()->get("message") . "%s");
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		$cmd = strtolower($command); //<-Changed Code
		switch($cmd){
			case "color":
				if($sender instanceof Player){
					$sender->sendMessage(TextFormat::BLUE . "========= ColorText =========");
					$sender->sendMessage(TextFormat::YELLOW . "=========== NOTE ===========");
					$sender->sendMessage(TextFormat::YELLOW . "Use /color to disable/enable colors in chat");
					if($this->coloredPlayers->exists($sender->getName(), true)){
						$this->coloredPlayers->remove($sender->getName(), true); // whether this works when players change the case of their names (e.g. from Heromine to HeroMine) depends on https://github.com/PocketMine/PocketMine-MP/pull/2226
						$sender->sendMessage(TextFormat::RED . "You have disabled color chat!");
						$sender->setRemoveFormat(true);
					}else{
						$this->coloredPlayers->set($sender->getName());
						$sender->sendMessage(TextFormat::GREEN . "You have enabled color chat!");
						$sender->setRemoveFormat(false);
						// who put a break here?
					}
				}
				return true;
		}
		return false;
	}
}
