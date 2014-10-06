<?php

namespace ColorText;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player; //<- Added This
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;

class ColorText extends PluginBase implements Listener{
   private $coloredChatPlayers=array();

   public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
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
         if(isset($this->coloredChatPlayers[$players->getName()])){
            $players->sendMessage("§7<".$player->getName()."> " .$message);//setMessage doesnt work in BigBrother?
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
		            $sender->sendMessage(TextFormat::YELLOW . "==========COLOR========== ");
              if(in_array($sender->getName(),$this->coloredChatPlayers)){
                 unset($this->coloredChatPlayers[$sender->getName()]);
                 $sender->sendMessage(TextFormat::RED . "You have disabled color chat!");
              }else{
                 array_push($this->coloredChatPlayers,$sender->getName());
                 $sender->sendMessage(TextFormat::BLUE . "You have enabled color chat!");
              }
           }
           return true;
      }
   }
}
