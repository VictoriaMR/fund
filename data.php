<?php
header('Access-Control-Allow-Origin: *');
header('application/json;charset=UTF-8');

$param = file_get_contents('php://input');
$codeArr = json_decode($param, true);
if (empty($codeArr)) {
	exit('12312');
}
$he = array_sum(array_keys($codeArr));
$result = ['list'=>[], 'total' => 0];
foreach ($codeArr as $key => $code) {
	$data = file_get_contents('http://fundgz.1234567.com.cn/js/'.$key.'.js?rt='.time());
	$data = json_decode(rtrim(ltrim($data, 'jsonpgz('), ');'), true);
	$result['list'][] = [
		'name' => $data['name'],
		'code' => $data['fundcode'],
		'percent' => $data['gszzl'],
		'free' => $code['free'],
		'win' => sprintf('%.2f', $code['free'] * $data['gszzl'] / 100),
	];
}
$result['total'] = sprintf('%.2f', array_sum(array_column($result['list'], 'win')));
echo json_encode($result);
exit();
