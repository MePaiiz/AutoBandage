<?php

namespace autoheal;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\SimpleCommandMap;
use pocketmine\command\ConsoleCommandSender; 
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\level\Explosion;
class Main extends PluginBase implements Listener {
    
    private $heal1 = [];
    
    function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->setting = new Config($this->getDataFolder()."settings.yml", Config::YAML, array(
            "permissionhp" => "auto.heal",
            "lowhp" => 10,
            "healhp" => 20,
            "healid" => 260,
            "cooldownhp" => 3,
            "message" => [
                "heal" => "§l§eAuto§aHeal§9: §fได้ปั้มยาไห้คุณแล้ว!",
                "cooldown" => "§l§eAuto§aHeal\n§l§fกำลังคลูดาวน์..."
                ]
        ));
    }

    function onMove(PlayerMoveEvent $ev){
        $p = $ev->getPlayer();
        $per = $this->setting->get("permissionhp");
        $low = $this->setting->get("lowhp");
        $heal = $this->setting->get("healhp");
        $id = $this->setting->get("healid");
        $cool = $this->setting->get("cooldownhp");
        $mes1 = $this->setting->get("message")["heal"];
        $mes2 = $this->setting->get("message")["cooldown"];
        if($p->hasPermission($per)){
            if($p->getHealth() < $low){
                for( $i=1; $i<=10000; $i++ ) {
                if($p->getInventory()->contains(Item::get($id, 0, $i))){
                    if(!isset($this->heal1[strtolower($p->getName())]) or time() > $this->heal1[strtolower($p->getName())]) {
                        $p->getInventory()->removeItem(Item::get($id,0,1));
                        $this->heal1[strtolower($p->getName())] = time() + $cool;
                        $p->setHealth($p->getHealth() + $heal);
                        $p->sendPopup($mes1); 
                    }else{
                        $l = $this->heal1[strtolower($p->getName())] - time();
                        $p->sendPopup($mes2);
                    }
                }
                }
            }
        }
    }
}
