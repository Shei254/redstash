<?php
/**
 * Copyright (c) 2012, ideawu
 * All rights reserved.
 * @author: ideawu
 * @link: http://www.ideawu.com/
 *
 * unit test.
 */

include(dirname(__FILE__) . '/../api/php/REDSTASH.php');

class REDSTASHTest extends UnitTest{
	private $redstash;

	function __construct(){
		$host = '127.0.0.1';
		$port = 8888;
		$this->redstash = new SimpleREDSTASH($host, $port);
		$this->redstash->auth('very-strong-password-11111111111111111');
		$this->clear();
	}

	function clear(){
		$redstash = $this->redstash;
		$deleted = 0;
		while(1){
			$ret = $redstash->scan('TEST_', 'TEST_'.pack('C', 255), 1000);
			if(!$ret){
				break;
			}
			foreach($ret as $k=>$v){
				$redstash->del($k);
				$deleted += 1;
			}
		}
		while(1){
			$names = $redstash->hlist('TEST_', 'TEST_'.pack('C', 255), 1000);
			if(!$names){
				break;
			}
			foreach($names as $name){
				$deleted += $redstash->hclear($name);
				$ret = $redstash->hsize($name);
				$this->assert($ret == 0);
			}
		}
		while(1){
			$names = $redstash->zlist('TEST_', 'TEST_'.pack('C', 255), 1000);
			if(!$names){
				break;
			}
			foreach($names as $name){
				$deleted += $redstash->zclear($name);
				$ret = $redstash->zsize($name);
				$this->assert($ret == 0);
			}
		}
		while(1){
			$names = $redstash->qlist('TEST_', 'TEST_'.pack('C', 255), 1000);
			if(!$names){
				break;
			}
			foreach($names as $name){
				$deleted += $redstash->qclear($name);
				$ret = $redstash->qsize($name);
				$this->assert($ret == 0);
			}
		}
		if($deleted > 0){
			echo "clear $deleted\n";
		}
	}

	function test_kv(){
		$redstash = $this->redstash;
		$val = str_repeat(mt_rand(), mt_rand(1, 100));
		
		$redstash->del('TEST_a');
		$ret = $redstash->ttl('TEST_a');
		$this->assert($ret === -1);
		$ret = $redstash->expire('TEST_a', 10);
		$this->assert($ret === 0);
		$redstash->set('TEST_a', $val);
		$ret = $redstash->expire('TEST_a', 10);
		$this->assert($ret === 1);
		
		$redstash->setx('TEST_a', $val, 1);
		$ret = $this->redstash->get('TEST_a');
		$this->assert($ret === $val);
		usleep(1.5 * 1000 * 1000);
		$ret = $this->redstash->get('TEST_a');
		$this->assert($ret === null);

		$redstash->set('TEST_a', $val);
		$redstash->set('TEST_b', $val);
		
		$ret = $this->redstash->get('TEST_a');
		$this->assert($ret === $val);

		$ret = $redstash->scan('TEST_', 'TEST_'.pack('C', 255), 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->scan('TEST_a', 'TEST_'.pack('C', 255), 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->scan('TEST_b', 'TEST_'.pack('C', 255), 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->scan('TEST_', 'TEST_a', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->scan('TEST_', 'TEST_b', 10);
		$this->assert(count($ret) == 2);

		$ret = $redstash->rscan('TEST_'.pack('C', 255), 'TEST_', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->rscan('TEST_b', 'TEST_'.pack('C', 0), 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->rscan('TEST_a', 'TEST_'.pack('C', 0), 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->rscan('TEST_'.pack('C', 255), 'TEST_a', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->rscan('TEST_'.pack('C', 255), 'TEST_b', 10);
		$this->assert(count($ret) == 1);

		$ret = $redstash->keys('TEST_', 'TEST_'.pack('C', 255), 10);
		$this->assert(count($ret) == 2);

		$kvs = array();
		for($i=0; $i<5; $i++){
			$kvs['TEST_' . $i] = $i;
			$redstash->multi_set($kvs);
			$ret = $redstash->multi_get(array_keys($kvs));
			$this->assert(count($ret) == count($kvs));
			$ret = $redstash->multi_del(array_keys($kvs));
			$ret = $redstash->multi_get(array_keys($kvs));
			$this->assert(count($ret) == 0);
		}

		$ret = $redstash->exists('TEST_a');
		$this->assert($ret === true);
		$redstash->del('TEST_a');
		$ret = $redstash->exists('TEST_a');
		$this->assert($ret === false);
		$ret = $redstash->get('TEST_a');
		$this->assert($ret === null);
		$redstash->del('TEST_b');
		
		$redstash->del('TEST_a');
		$ret = $redstash->setnx('TEST_a', 'a');
		$this->assert($ret === 1);
		$ret = $redstash->setnx('TEST_a', 't');
		$this->assert($ret === 0);
		$ret = $redstash->get('TEST_a');
		$this->assert($ret === 'a');
		
		$redstash->del('TEST_a');
		$ret = $redstash->getset('TEST_a', 'a');
		$this->assert($ret === null);
		$ret = $redstash->getset('TEST_a', 'b');
		$this->assert($ret === 'a');
		$ret = $redstash->get('TEST_a');
		$this->assert($ret === 'b');

		$key = 'TEST_a';
		$redstash->del($key);
		$ret = $redstash->setbit($key, 8, 1);
		$this->assert($ret === 0);
		$ret = $redstash->setbit($key, 8, 1);
		$this->assert($ret === 1);
		$ret = $redstash->countbit($key, 0, 1);
		$this->assert($ret === 0);
		$ret = $redstash->countbit($key, 0, 2);
		$this->assert($ret === 1);
		$ret = $redstash->countbit($key, 0);
		$this->assert($ret === 1);
		$ret = $redstash->strlen($key);
		$this->assert($ret === 2);
		$val = '0123456789';
		$redstash->set($key, $val);
		$this->assert($redstash->substr($key, 0, 1) === substr($val, 0, 1));
		$this->assert($redstash->substr($key, -1, -1) === substr($val, -1, -1));
		$this->assert($redstash->substr($key, 0, -1) === substr($val, 0, -1));
		$this->assert($redstash->substr($key, -1, -2) === substr($val, -1, -2));
		$this->assert($redstash->substr($key, -2, -1) === substr($val, -2, -1));
		$this->assert($redstash->substr($key, -2, 2) === substr($val, -2, 2));
	}
	
	function test_queue(){
		$redstash = $this->redstash;
		$name = "TEST_" . str_repeat(mt_rand(), mt_rand(1, 3));
		$key = "TEST_" . str_repeat(mt_rand(), mt_rand(1, 3));
		$val = str_repeat(mt_rand(), mt_rand(1, 30));
				
		for($i=0; $i<7; $i++){
			$size = $redstash->qpush($name, $i);
			$this->assert($size === $i + 1);
		}
		$size = $redstash->qpush($name, array(7,8,9));
		$this->assert($size == 10);
		
		$ret = $redstash->qget($name, 3);
		$this->assert($ret == 3);
		$ret = $redstash->qslice($name, 0, -1);
		for($i=0; $i<10; $i++){
			$this->assert($ret[$i] == $i);
		}
		$ret = $redstash->qsize($name);
		$this->assert($ret === 10);
		$ret = $redstash->qfront($name);
		$this->assert($ret == 0);
		$ret = $redstash->qback($name);
		$this->assert($ret == 9);
		for($i=0; $i<10; $i++){
			$ret = $redstash->qpop($name);
			if($ret != $i){
				$this->assert(false);
				break;
			}
		}

		$ret = $redstash->qfront($name);
		$this->assert($ret === null);
		$ret = $redstash->qback($name);
		$this->assert($ret === null);
		
		$redstash->qpush_back($name, 0);
		$redstash->qpush_front($name, 9);
		$ret = $redstash->qfront($name);
		$this->assert($ret == 9);
		$ret = $redstash->qback($name);
		$this->assert($ret == 0);

		$redstash->qclear($name);
		for($i=0; $i<7; $i++){
			$size = $redstash->qpush_back($name, $i);
		}
		$ret = $redstash->qpop_front($name, 2);
		$this->assert(is_array($ret));
		$this->assert(count($ret) == 2);
		$this->assert($ret[0] == 0);
		$this->assert($ret[1] == 1);
		
		$ret = $redstash->qpop_back($name, 2);
		$this->assert(is_array($ret));
		$ret = $redstash->qpop($name, 2);
		$this->assert(is_array($ret));

		$redstash->qclear($name);
		for($i=0; $i<3; $i++){
			$redstash->qpush_back($name, $i);
		}

		$ret = $redstash->qset($name, 0, 'www');
		$this->assert($ret !== false);
		$ret = $redstash->qset($name, 9990, 'www');
		$this->assert($ret === false);
		$ret = $redstash->qget($name, 0);
		$this->assert($ret === 'www');

		$ret = $redstash->qtrim_front($name, 2);
		$this->assert($ret === 2);
		$ret = $redstash->qtrim_back($name, 2);
		$this->assert($ret === 1);
	}

	function test_hash(){
		$redstash = $this->redstash;
		$name = "TEST_" . mt_rand();
		$key = "TEST_" . mt_rand();
		$val = str_repeat(mt_rand(), mt_rand(1, 30));

		$ret = $redstash->hsize($name);
		$this->assert($ret === 0);

		$ret = $redstash->multi_hset($name, array('a' => 1, 'a' => 2));
		$this->assert($ret == 1);
		$ret = $redstash->multi_hdel($name, array('a', 'a'));
		$this->assert($ret == 1);

		$ret = $redstash->hset($name, $key, $val);
		$ret = $redstash->hexists($name, $key);
		$this->assert($ret);
		$ret = $redstash->hget($name, $key);
		$this->assert($ret === $val);

		$ret = $redstash->hsize($name);
		$this->assert($ret === 1);
		$ret = $redstash->hscan($name, '', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hrscan($name, '', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hkeys($name, '', '', 10);
		$this->assert(count($ret) == 1);

		$ret = $redstash->hdel($name, $key);
		$ret = $redstash->hsize($name);
		$this->assert($ret === 0);
		$ret = $redstash->hscan($name, '', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->hrscan($name, '', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->hkeys($name, '', '', 10);
		$this->assert(count($ret) == 0);

		$ret = $redstash->hset($name, 'a', $val);
		$ret = $redstash->hset($name, 'b', $val);
		$ret = $redstash->hscan($name, '', '', 10);
		$this->assert(count($ret) == 2);
		foreach($ret as $k=>$v){
			$this->assert($v === $val);
		}
		$ret = $redstash->hscan($name, '', 'a', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hscan($name, '', 'b', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->hrscan($name, '', 'b', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hrscan($name, '', 'a', 10);
		$this->assert(count($ret) == 2);

		$ret = $redstash->hscan($name, 'a', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hscan($name, 'b', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->hrscan($name, '', '', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->hrscan($name, 'b', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hrscan($name, 'a', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->hkeys($name, '', '', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->hkeys($name, 'a', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->hkeys($name, 'b', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->hdel($name, 'a');
		$ret = $redstash->hdel($name, 'b');

		$redstash->hset("TEST_a", 'a', 1);
		$redstash->hset("TEST_b", 'a', 1);
		$redstash->hset("TEST_c", 'a', 1);
		$ret = $redstash->hlist("TEST_a", "TEST_b", 100);
		$this->assert(count($ret) == 1);
		$this->assert($ret[0] == "TEST_b");

		$ret = $redstash->hexists('TEST_a', 'a');
		$this->assert($ret === true);
		$redstash->hdel('TEST_a', 'a');
		$ret = $redstash->hexists('TEST_a', 'a');
		$this->assert($ret === false);
		$ret = $redstash->hget('TEST_a', 'a');
		$this->assert($ret === null);
	}

	function test_zset(){
		$redstash = $this->redstash;
		$name = "TEST_" . mt_rand();
		$key = "TEST_" . mt_rand();
		$val = mt_rand();

		$ret = $redstash->zsize($name);
		$this->assert($ret === 0);

		$ret = $redstash->multi_zset($name, array('a' => 1, 'a' => 2));
		$this->assert($ret == 1);
		$ret = $redstash->multi_zdel($name, array('a', 'a'));
		$this->assert($ret == 1);

		$ret = $redstash->zset($name, $key, $val);
		$ret = $redstash->zexists($name, $key);
		$this->assert($ret);
		$ret = $redstash->zget($name, $key);
		$this->assert($ret === $val);

		$ret = $redstash->zsize($name);
		$this->assert($ret === 1);
		$ret = $redstash->zscan($name, '', '', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->zrscan($name, '', '', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->zkeys($name, '', '', '', 10);
		$this->assert(count($ret) == 1);

		$ret = $redstash->zdel($name, $key);
		$ret = $redstash->zsize($name);
		$this->assert($ret === 0);
		$ret = $redstash->zscan($name, '', '', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->zrscan($name, '', '', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->zkeys($name, '', '', '', 10);
		$this->assert(count($ret) == 0);

		$ret = $redstash->zset($name, 'a', $val);
		$ret = $redstash->zset($name, 'b', $val);

		$ret = $redstash->zrank($name, 'aaaaaaaa');
		$this->assert($ret === null);
		$ret = $redstash->zrank($name, 'a');
		$this->assert($ret != -1);
		$ret = $redstash->zrrank($name, 'a');
		$this->assert($ret != -1);

		$ret = $redstash->zrange($name, 0, 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->zrrange($name, 0, 10);
		$this->assert(count($ret) == 2);

		$ret = $redstash->zscan($name, '', '', '', 10);
		$this->assert(count($ret) == 2);
		foreach($ret as $k=>$v){
			$this->assert($v == $val);
		}
		$ret = $redstash->zscan($name, 'a', '', '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->zscan($name, 'b', '', '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->zrscan($name, '', '', '', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->zrscan($name, 'b', $val, '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->zrscan($name, 'a', $val, '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->zkeys($name, '', '', '', 10);
		$this->assert(count($ret) == 2);
		$ret = $redstash->zkeys($name, 'a', $val, '', 10);
		$this->assert(count($ret) == 1);
		$ret = $redstash->zkeys($name, 'b', $val, '', 10);
		$this->assert(count($ret) == 0);
		$ret = $redstash->zdel($name, 'a');
		$ret = $redstash->zdel($name, 'b');

		$redstash->zset("TEST_a", 'a', 1);
		$redstash->zset("TEST_b", 'a', 1);
		$redstash->zset("TEST_c", 'a', 1);
		$ret = $redstash->zlist("TEST_a", "TEST_b", 100);
		$this->assert(count($ret) == 1);
		$this->assert($ret[0] == "TEST_b");

		$ret = $redstash->zexists('TEST_a', 'a');
		$this->assert($ret === true);
		$redstash->zdel('TEST_a', 'a');
		$ret = $redstash->zexists('TEST_a', 'a');
		$this->assert($ret === false);
		$ret = $redstash->zget('TEST_a', 'a');
		$this->assert($ret === null);
		
		$redstash->zclear($name);
		$redstash->request('multi_zset', $name, 'a', '1', 'b', '2', 'c', '3', 'd', '4', 'e', '5');
		$ret = $redstash->zcount($name, 2, 4);
		$this->assert($ret === 3);
		$ret = $redstash->zsum($name, 2, 4);
		$this->assert($ret === 9);
		$ret = $redstash->zavg($name, 2, 3);
		$this->assert($ret === 2.5);
		$ret = $redstash->zRemRangeByScore($name, 4, 5);
		$this->assert($ret === 2);
		$ret = $redstash->zRemRangeByRank($name, 1, 2);
		$this->assert($ret === 2);

		$redstash->zclear($name);
		for($i=0; $i<10; $i++){
			$redstash->zset($name, $i, $i);
		}
		$ret = $redstash->zscan($name, '', 3, 10, 1);
		$vals = array_values($ret);
		$this->assert($vals[0] === 3);
		$ret = $redstash->zscan($name, '3', 3, 10, 1);
		$vals = array_values($ret);
		$this->assert($vals[0] === 4);

		$ret = $redstash->zrscan($name, '', 3, 1, 1);
		$vals = array_values($ret);
		$this->assert($vals[0] === 3);
		$ret = $redstash->zrscan($name, '3', 3, 1, 1);
		$vals = array_values($ret);
		$this->assert($vals[0] === 2);

		$redstash->zclear($name);
		for($i=0; $i<10; $i++){
			$redstash->zset($name, $i, $i);
		}
		$ret = $redstash->zpop_front($name, 2);
		$keys = array_keys($ret);
		$vals = array_values($ret);
		$this->assert($keys[0] === 0 && $vals[0] === 0);
		$this->assert($keys[1] === 1 && $vals[1] === 1);
		$ret = $redstash->zpop_back($name, 2);
		$keys = array_keys($ret);
		$vals = array_values($ret);
		$this->assert($keys[0] === 9 && $vals[0] === 9);
		$this->assert($keys[1] === 8 && $vals[1] === 8);
	}
}

class UnitTest{
	private $result = array(
			'passed' => 0,
			'failed' => 0,
			'tests' => array(
				),
			);

	function run(){
		$class_name = get_class($this);
		$methods = get_class_methods($class_name);
		foreach($methods as $method){
			if(strpos($method, 'test_') === 0){
				$this->$method();
			}
		}
		$this->report();
		$this->clear();
	}

	function report(){
		$res = $this->result;
		printf("passed: %3d, failed: %3d\n", $res['passed'], $res['failed']);
		foreach($res['tests'] as $test){
			if($test[0] === false){
				printf("    Failed: %s:%d %s() %s\n", $test[2], $test[3], $test[1], $test[4]);
			}
		}
		if($res['failed']){
			printf("passed: %3d, failed: %3d\n", $res['passed'], $res['failed']);
		}
	}

	function assert($val, $desc=''){
		if($val === true){
			$this->result['passed'] ++;
		}else{
			$val = false;
			$this->result['failed'] ++;
		}
		$bt = debug_backtrace(false);
		$func = $bt[1]['function'];
		$file = basename($bt[1]['file']);
		$line = $bt[0]['line'];
		$this->result['tests'][] = array(
				$val, $func, $file, $line, $desc
				);
	}

}


$test = new REDSTASHTest();
$test->run();

