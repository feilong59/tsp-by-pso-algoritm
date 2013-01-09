<?php
/**
 * 以粒子群最佳化演算法（Particle swarm optimization）解決旅行商問題（Travelling salesman problem）
 * 程式作者：康家豪
 */
require_once 'Particle.php';
require_once 'Swarm.php';

// 旅行點數量
define('__PONIT_MAX__', 20);

// 粒子數量
define('__SWARM_COUNT__', 50);

// 演算迭代量
define('__ITER_COUNT', 20);

// 毀滅次數
define('__DISTICNT_TIME', 10);

// 判斷是否產生新的旅行點產生隨機旅行點
if ($_POST['isGenerateNewPoints'] == 1 || count($_POST) == 0) {
	for ($i = 0; $i < __PONIT_MAX__; $i++) {
		$GLOBALS['travelPoints'][$i] = array(
			'0' => mt_rand(-300, 300),
			'1' => mt_rand(-300, 300),
			'e' => ''
		);
	}
} else {
	$GLOBALS['travelPoints'] = $_POST['pointsInput'];
}

// 將旅行點資料寫入為<Input>
for ($i = 0; $i < count($GLOBALS['travelPoints']); $i++) {
	$pointInputData .= '<input type="hidden" name="pointsInput[' . $i . '][0]" value="' . $GLOBALS['travelPoints'][$i][0] . '" />';
	$pointInputData .= '<input type="hidden" name="pointsInput[' . $i . '][1]" value="' . $GLOBALS['travelPoints'][$i][1] . '" />';
	$pointInputData .= '<input type="hidden" name="pointsInput[' . $i . '][e]" value="" />';
}

// 初始粒子群，開始進行計算，取得最佳結果
$result = array();
$swarm = new Swarm(__SWARM_COUNT__);
for ($d = 0; $d < __DISTICNT_TIME; $d++) {

	if ($d > 0) {
		$swarm->resetAll();
	}

	for ($i = 0; $i < __ITER_COUNT; $i++) {
		$swarm->applyParticleVelocity();
		$swarm->calculateParticleFitness();
		$swarm->findGlobalBest();
		$swarm->updateParticleVelocity();

	}

	if ($d == 0 || $swarm->getBestFitness() < $result[fitness]) {
		$result[fitness] = $swarm->getBestFitness();
		$result[position] = $swarm->getBestPosition();
	}
}

// 設定樣板資料（最佳路徑長度、路徑順序、平均適應值歷史資料）
$routeLength = round($result[fitness], 2);
$route = $result[position];
ksort($route);
$route = json_encode($route);
$fitnessHistory = $swarm->getFitnessHistory();

// 進行旅行點座標轉換
foreach ($GLOBALS['travelPoints'] as $p) {
	$p[0] += 340;
	$p[1] = -($p[1] - 340);
	$points[] = $p;
}
$points = json_encode($points);

// 載入顯示樣板
require 'template.php';
?>