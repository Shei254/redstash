<?php
/**
 * Copyright (c) 2012, ideawu
 * All rights reserved.
 * @author: ideawu
 * @link: http://www.ideawu.com/
 *
 * REDSTASH PHP API demo.
 */

include(dirname(__FILE__) . '/REDSTASH.php');
$host = '127.0.0.1';
$port = 8888;


try{
	$redstash = new SimpleREDSTASH($host, $port);
	//$redstash->easy();
}catch(Exception $e){
	die(__LINE__ . ' ' . $e->getMessage());
}

var_dump($redstash->set('test', time()));
var_dump($redstash->set('test', time()));
echo $redstash->get('test') . "\n";
var_dump($redstash->del('test'));
var_dump($redstash->del('test'));
var_dump($redstash->get('test'));
echo "\n";

var_dump($redstash->hset('test', 'b', time()));
var_dump($redstash->hset('test', 'b', time()));
echo $redstash->hget('test', 'b') . "\n";
var_dump($redstash->hdel('test', 'b'));
var_dump($redstash->hdel('test', 'b'));
var_dump($redstash->hget('test', 'b'));
echo "\n";

var_dump($redstash->zset('test', 'a', time()));
var_dump($redstash->zset('test', 'a', time()));
echo $redstash->zget('test', 'a') . "\n";
var_dump($redstash->zdel('test', 'a'));
var_dump($redstash->zdel('test', 'a'));
var_dump($redstash->zget('test', 'a'));
echo "\n";

$redstash->close();

die();

/* a simple bench mark */

$data = array();
for($i=0; $i<1000; $i++){
	$k = '' . mt_rand(0, 100000);
	$v = mt_rand(100000, 100000 * 10 - 1) . '';
	$data[$k] = $v;
}

speed();
try{
	$redstash = new REDSTASH($host, $port);
}catch(Exception $e){
	die(__LINE__ . ' ' . $e->getMessage());
}
foreach($data as $k=>$v){
	$ret = $redstash->set($k, $v);
	if($ret === false){
		echo "error\n";
		break;
	}
}
$redstash->close();
speed('set speed: ', count($data));


speed();
try{
	$redstash = new REDSTASH($host, $port);
}catch(Exception $e){
	die(__LINE__ . ' ' . $e->getMessage());
}
foreach($data as $k=>$v){
	$ret = $redstash->get($k);
	if($ret === false){
		echo "error\n";
		break;
	}
}
$redstash->close();
speed('get speed: ', count($data));



function speed($msg=null, $count=0){
	static $stime;
	if(!$msg && !$count){
		$stime = microtime(1);
	}else{
		$etime = microtime(1);
		$ts = ($etime - $stime == 0)? 1 : $etime - $stime;
		$speed = $count / floatval($ts);
		$speed = sprintf('%.2f', $speed);
		echo "$msg: " . $speed . "\n";

		$stime = $etime;
	}
}
