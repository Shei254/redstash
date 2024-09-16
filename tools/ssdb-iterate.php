<?php
/**
 * Copyright (c) 2017, ideawu
 * All rights reserved.
 * @author: ideawu
 * @link: http://www.ideawu.com/
 *
 * Demonstrate how to iterate over the whole db.
 */

include(dirname(__FILE__) . '/../api/php/REDSTASH.php');

$host = '127.0.0.1';
$port = 8888;
$redstash = new SimpleREDSTASH($host, $port);

// to copy data from one redstash to another
// $dst = new SimpleREDSTASH($host2, $port2);

$size = 1000;

// KV
$s_key = ''; // the lower bound of key range to iterate over(exclusive)
$e_key = ''; // the upper bound of key range to iterate over(inclusive)
while(1){
	$kvs = $redstash->scan($s_key, $e_key, $size);
	if(!$kvs){
		break;
	}
	foreach($kvs as $k=>$v){
		$s_key = $k;
		// do your stuff
		// to copy data
		// $dst->set($k, $v);
		echo "KV: $k\n";
	}
}


// HASH
$s_key = ''; // the lower bound of key range to iterate over(exclusive)
$e_key = ''; // the upper bound of key range to iterate over(inclusive)
while(1){
	$names = $redstash->hlist($s_key, $e_key, $size);
	if(!$names){
		break;
	}
	foreach($names as $name){
		$s_key = $name;

		$s_item = '';
		while(1){
			$kvs = $redstash->hscan($name, $s_item, '', $size);
			if(!$kvs){
				break;
			}
			foreach($kvs as $k=>$v){
				$s_item = $k;
				// do your stuff
				// to copy data
				// $dst->hset($name, $k, $v);
				echo "HASH: $name - $k\n";
			}
		}
	}
}


// ZSET
$s_key = ''; // the lower bound of key range to iterate over(exclusive)
$e_key = ''; // the upper bound of key range to iterate over(inclusive)
while(1){
	$names = $redstash->zlist($s_key, $e_key, $size);
	if(!$names){
		break;
	}
	foreach($names as $name){
		$s_name = $name;

		$s_item = '';
		$s_score = '';
		while(1){
			$kvs = $redstash->zscan($name, $s_item, $s_score, '', $size);
			if(!$kvs){
				break;
			}
			foreach($kvs as $k=>$v){
				$s_item = $k;
				$s_score = $v;
				// do your stuff
				// to copy data
				// $dst->zset($name, $k, $v);
				echo "ZSET: $name - $k : $v\n";
			}
		}
	}
}


// QUEUE/LIST
$s_key = ''; // the lower bound of key range to iterate over(exclusive)
$e_key = ''; // the upper bound of key range to iterate over(inclusive)
while(1){
	$names = $redstash->qlist($s_key, $e_key, $size);
	if(!$names){
		break;
	}
	foreach($names as $name){
		$s_key = $name;

		$start = 0;
		while(1){
			$kvs = $redstash->qrange($name, $start, $size);
			if(!$kvs){
				break;
			}
			foreach($kvs as $k=>$v){
				$start ++;
				$index = $start - 1;
				// do your stuff
				// to copy data
				// $dst->qpush($name, $v);
				echo "LIST: $name - [$index]\n";
			}
		}
	}
}
