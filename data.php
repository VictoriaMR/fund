<?php
header('Access-Control-Allow-Origin: *');
header('application/json;charset=UTF-8');

$param = file_get_contents('php://input');
$codeArr = json_decode($param, true);
if (empty($codeArr)) {
	exit('12312');
}
$result = ['list'=>[], 'total' => 0];
foreach ($codeArr as $key => $code) {
	$data = @file_get_contents('http://fundgz.1234567.com.cn/js/'.$key.'.js?rt='.time());
	if (empty($data)) continue;
	$data = json_decode(rtrim(ltrim($data, 'jsonpgz('), ');'), true);
	$result['list'][] = [
		'name' => $data['name'],
		'code' => $data['fundcode'],
		'percent' => $data['gszzl'],
		'free' => $code['free'],
		'win' => sprintf('%.2f', $code['free'] * $data['gszzl'] / 100),
	];
}
if (!empty($result['list'])) {
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
