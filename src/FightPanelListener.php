<?php

declare(strict_types=1);

namespace fightPanel;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class FightPanelListener implements Listener{

	public function __construct(private MainClass $plugin){ }

	public function onPlayerMissSwing(PlayerMissSwingEvent $event):void{
		// 记录击打次数
		$playerName = $event->getPlayer()->getName();
		if(!array_key_exists($playerName, $this->plugin->attackCount)){
			$this->plugin->attackCount[$playerName] = array();
		}
		$this->plugin->attackCount[$playerName][] = $this->plugin->get_total_millisecond();
	}

	public function onEntityDamageByEntity(EntityDamageByEntityEvent $event):void{
		$this->plugin->getLogger()->info(
			$event->getDamager()->getName().
			" 攻击了 ".
			$event->getEntity()->getName().
			" 造成了 ".
			$event->getFinalDamage().
			" 点伤害"
		);

		// 记录击打次数
		$playerName = $event->getDamager()->getName();
		if(!array_key_exists($playerName, $this->plugin->attackCount)){
			$this->plugin->attackCount[$playerName] = array();
		}
		$this->plugin->attackCount[$playerName][] = $this->plugin->get_total_millisecond();

		// 记录输出伤害量
		$playerName = $event->getDamager()->getName();
		if(Server::getInstance()->getPlayerExact($playerName) == null){
			return;
		}
		if(!array_key_exists($playerName, $this->plugin->outDamageCount)){
			$this->plugin->outDamageCount[$playerName] = array();
		}
		$this->plugin->outDamageCount[$playerName][] = [
			$this->plugin->get_total_millisecond(), $event->getFinalDamage()
		];
		
		// 记录输入伤害量
		$playerName = $event->getEntity()->getName();
		if(Server::getInstance()->getPlayerExact($playerName) == null){
			return;
		}
		if(!array_key_exists($playerName, $this->plugin->inDamageCount)){
			$this->plugin->inDamageCount[$playerName] = array();
		}
		$this->plugin->inDamageCount[$playerName][] = [
			$this->plugin->get_total_millisecond(), $event->getFinalDamage()
		];
	}
}
