<?php
header('Access-Control-Allow-Origin: *');
header('application/json;charset=UTF-8');
ini_set('date.timezone','Asia/Shanghai');

$param = file_get_contents('php://input');
$codeArr = json_decode($param, true);
$result = ['list'=>[], 'total' => 0, 'total_percent' => 0];
foreach ($codeArr as $key => $code) {
	$data = @file_get_contents('http://fundgz.1234567.com.cn/js/'.$key.'.js?rt='.time());
	if (!empty($data)) {
		$data = json_decode(rtrim(ltrim($data, 'jsonpgz('), ');'), true);
	}
	$result['list'][$key] = [
		'name' => $data['name'] ?? '',
		'code' => $key,
		'percent' => $data['gszzl'] ?? 0,
		'free' => $code['free'],
		'free_update' => $code['free_update'] ?? 0,
		'win' => sprintf('%.2f', $code['free'] * $data['gszzl'] ?? 1 / 100),
	];
}
if (!empty($result['list'])) {
	$temp = array_column($result['list'], 'win', 'code');
	arsort($temp, SORT_NUMERIC);
	$arr = [];
	foreach ($temp as $key => $value) {
		$arr[] = $result['list'][$key];
	}
	$result['list'] = $arr;
	$he = array_sum(array_column($result['list'], 'free'));
	$result['total'] = sprintf('%.2f', array_sum(array_column($result['list'], 'win')));
	$result['total_percent'] = sprintf('%.2f', $result['total'] / $he * 100);
}
$result['can_update'] = false;
if (date('H') >= 15) {
	$result['can_update'] = true;
}
echo json_encode($result);
exit();
