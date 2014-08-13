<?php

namespace ColorText;

use pocketmine\server;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerChatEvent;

class ColorText extends PluginBase implements Listener{

private $coloredChatPlayers=array();
public function onChat(PlayerChatEvent $event){
   $player = $event->getPlayer();
   $message = $event->getMessage();
   foreach($this->getServer()->getOnlinePlayers() as $players){
      if(isset($this->coloredChatPlayers[$players])){
         $players->setRemoveFormat(false);
         $players->sendMessage("§7<".$player->getName()."> ".$message);//should send message in grey
      }else{
         $players->sendMessage("<".$player->getName()."> ".$message);
      }
   }
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
