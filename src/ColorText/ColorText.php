<?php

namespace ColorText;

use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class ColorText extends PluginBase implements CommandExecutor,Listener{
   private $coloredChatPlayers=array();

   public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
   }

   /**
     * @param PlayerChatEvent $event
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */
   public function onChat(PlayerChatEvent $event){
      $player = $event->getPlayer();
      $message = $event->getMessage();
      foreach($this->getServer()->getOnlinePlayers() as $players){
         if(isset($this->coloredChatPlayers[$players->getName()])){
            $players->setRemoveFormat(false);
            $players->sendMessage("§7<".$player->getName()."> ".$message);//setMessage doesnt work in BigBrother?
         }else{
            $players->sendMessage("<".$player->getName()."> ".$message);
         }
      }
      $event->setCancelled(true);
   }
   
   public function onCommand(CommandSender $sender, Command $command, $label, array $args){
      $cmd = $command->getName();
      if($sender instanceof Player){
         if($cmd=="color"){
            if(in_array($sender->getName(),$this->coloredChatPlayers)){
               unset($this->coloredChatPlayers[$sender->getName()]);
               $sender->sendMessage("You have disabled color chat!");
            }else{
               array_push($this->coloredChatPlayers,$sender->getName());
               $sender->sendMessage("You have enabled color chat!");
            }
         }
      }
   }
}
