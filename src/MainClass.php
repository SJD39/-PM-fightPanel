<?php

declare(strict_types=1);

namespace fightPanel;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class MainClass extends PluginBase{
	public Config $config;

	public $enlable = true;
	// 0 一直发送
	// 1 触发时发送
	public $sendTime = 0;

	// 每秒攻击次数
	public array $attackCount = [];
	// 每秒造成伤害
	public array $outDamageCount = [];
	// 每秒受到伤害
	public array $inDamageCount = [];

	public array $onlinePlayers = [];

	public function onLoad() : void{
		$this->getLogger()->info(TextFormat::WHITE . "战斗面板加载中！");

		// 处理配置文件
		$this->config = new Config($this->getDataFolder() . "config.json", Config::JSON, [
			"format" => "CPS: {cps} DPS: {dps} HPS: {hps}"
		]);
	}

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(new FightPanelListener($this), $this);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTask($this, $this->getServer()), 2);
		$this->getLogger()->info(TextFormat::DARK_GREEN . "战斗面板启动！");
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . "战斗面板关闭！");
	}

	/**
	 * 取十三位时间戳
	 */
	public function get_total_millisecond() : int{
		return (int) microtime(true) * 1000;
	}
}
