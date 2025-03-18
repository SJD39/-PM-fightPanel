<?php

declare(strict_types=1);

namespace fightPanel;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class MainClass extends PluginBase{
	// 每秒攻击次数
	public $attackCount = array();
	// 每秒造成伤害
	public $outDamageCount = array();
	// 每秒受到伤害
	public $inDamageCount = array();

	public $onlinePlayers = [];

	public function onLoad() : void{
		$this->getLogger()->info(TextFormat::WHITE . "战斗面板加载中！");
	}

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(new FightPanelListener($this), $this);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this, $this->getServer()), 2);
		$this->getLogger()->info(TextFormat::DARK_GREEN . "战斗面板启动！");
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . "战斗面板关闭！");
	}

	public function get_total_millisecond()
	{
		$time = explode(" ", microtime());
		$time = ($time [1] + $time [0]) * 1000;
		$time = round($time) . '';
		return $time;
	}

	public function fromNameGetPlayer($name){
		$this->onlinePlayers = $this->getServer()->getOnlinePlayers();

		foreach($this->onlinePlayers as $player){
			if($player->getName() == $name){
				return $player;
			}
		}
		return null;
	}
}
