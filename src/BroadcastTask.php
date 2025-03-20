<?php

declare(strict_types=1);

namespace fightPanel;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class BroadcastTask extends Task{

	public function __construct(private MainClass $plugin, private Server $server){}

	public function onRun() : void{
		// 遍历每个玩家点击计数数组
		foreach($this->plugin->attackCount as $playerName => $playerAttackCount){
			// 遍历玩家点击计数
			foreach($playerAttackCount as $key => $value){
				if($this->plugin->get_total_millisecond() - $value > 1000){
					array_splice($this->plugin->attackCount[$playerName], $key, 1);
				}else{
					break;
				}
			}
		}

		// 定时移出超过1秒的伤害计数
		foreach($this->plugin->outDamageCount as $playerName => $playerDamageCount){
			foreach($playerDamageCount as $key => $value){
				if($this->plugin->get_total_millisecond() - $value[0] > 1000){
					array_splice($this->plugin->outDamageCount[$playerName], $key, 1);
				}else{
					break;
				}
			}
		}

		// 定时移出超过1秒的受伤计数
		foreach($this->plugin->inDamageCount as $playerName => $playerDamageCount){
			foreach($playerDamageCount as $key => $value){
				if($this->plugin->get_total_millisecond() - $value[0] > 1000){
					array_splice($this->plugin->inDamageCount[$playerName], $key, 1);
				}else{
					break;
				}
			}
		}

		// 遍历在线玩家，发送弹窗
		$onlinePlayers = $this->server->getOnlinePlayers();
		foreach($onlinePlayers as $player){
			$playerName = $player->getName();

			if(!array_key_exists($playerName, $this->plugin->attackCount)){
				$this->plugin->attackCount[$playerName] = [];
			}
			if(!array_key_exists($playerName, $this->plugin->outDamageCount)){
				$this->plugin->outDamageCount[$playerName] = [];
			}
			if(!array_key_exists($playerName, $this->plugin->inDamageCount)){
				$this->plugin->inDamageCount[$playerName] = [];
			}

			$cps = count($this->plugin->attackCount[$playerName]);
			$dps = 0;
			foreach($this->plugin->outDamageCount[$playerName] as $value){
				$dps += $value[1];
			}
			$hps = 0;
			foreach($this->plugin->inDamageCount[$playerName] as $value){
				$hps += $value[1];
			}

			$hitCount = 0;
			$missCount = 0;
			if(!array_key_exists($playerName, $this->plugin->hitCount)){
				$this->plugin->hitCount[$playerName] = [];
			}
			foreach($this->plugin->hitCount[$playerName] as $value){
				if($value[1]){
					$hitCount++;
				}else{
					$missCount++;
				}
			}
			if($hitCount == 0 || $missCount == 0){
				$hitRate = ($hitCount == 0) ? 0 : 1;
			}else{
				$hitRate = $hitCount / ($hitCount + $missCount);
			}

			$sendPopupValue = $this->plugin->config->get("format");
			$player->sendPopup(
				str_replace(
					["{cps}", "{dps}", "{hps}", "{hitRate}"],
					[$cps, $dps, $hps, ($hitRate * 100) . "%"],
					$sendPopupValue
				)
			);
		}
	}
}
