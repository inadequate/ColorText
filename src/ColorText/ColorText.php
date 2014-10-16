<?php

namespace ColorText;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player; //<- forgot to include Player library
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;

class ColorText extends PluginBase implements Listener{
   private static $coloredChatPlayers=[]; //<- variable should be static for use self.
   private static $config;
 
   public function onEnable(){
	     @mkdir($this->getDataFolder()); //<-Create plugin directory
	     $this->saveDefaultConfig(); //<-Save default config
	     self::$config = $this->getConfig()->getAll(); //<- Get config
	     //Checking config variables
	     if(!isset(self::$config["enabled"]) || !isset(self::$config["name"]) || !isset(self::$config["message"])){
		       //Saving Defaults
		       self::$config["enabled"] = true;
		       self::$config["name"] = "§2";
		       self::$config["message"] = "§d";
		       $this->getConfig()->setAll(self::$config);
		       $this->getConfig()->save();
		    }
	     if(self::$config["enabled"] != true){
		       $this->getLogger()->info("Plugin disabled");
		      $this->getServer()->getPluginManager()->disablePlugin($this);
		    }else{
         $this->getServer()->getPluginManager()->registerEvents($this, $this);
      }
      foreach($this->getServer()->getOnlinePlayers() as $player){
         $player->setRemoveFormat(false);
      }
   }
   /**
    * @priority MONITOR
    * @ignoreCancelled true
    */
   public function onJoin(PlayerJoinEvent $ev){
      $ev->getPlayer()->setRemoveFormat(false);
   }
   /**
     * @param PlayerChatEvent $event
     *
     * @priority LOWEST
     * @ignoreCancelled false
     */
   public function onChat(PlayerChatEvent $event){
      $player = $event->getPlayer();
      $message = $event->getMessage();
      foreach($this->getServer()->getOnlinePlayers() as $players){
         if(isset(self::$coloredChatPlayers[$players->getName()])){ //<-self:: used to get/set a shared variable
            $players->sendMessage(self::$config["name"]."<".$player->getName().">§d ".self::$config["message"].$message);//setMessage doesnt work in BigBrother?
         }else{
            $players->sendMessage("<".$player->getName()."> ".$message);
         }
      }
      $event->setCancelled(true);
   }
   
   public function onCommand(CommandSender $sender, Command $command, $label, array $args){
      $cmd = strtolower($command); //<-Changed Code
      switch($cmd){
	        case "color":
	          if($sender instanceof Player){
              if(isset(self::$coloredChatPlayers[$sender->getName()])){ //<-you can't use in_array with this type of array
                 unset(self::$coloredChatPlayers[$sender->getName()]);
                 $sender->sendMessage(TextFormat::RED . "You have disabled color chat!");
                 break; //<-break is required to stop command execution
              }else{
                 self::$coloredChatPlayers[$sender->getName()] = "";
                 $sender->sendMessage(TextFormat::GREEN . "You have enabled color chat!");
                 break;
              }
           }
           return true;
      }
   }
}
