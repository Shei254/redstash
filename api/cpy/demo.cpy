/**
 * Copyright (c) 2012, ideawu
 * All rights reserved.
 * @author: ideawu
 * @link: http://www.ideawu.com/
 *
 * REDSTASH cpy API demo.
 */

import REDSTASH.REDSTASH;

try{
	redstash = new REDSTASH('127.0.0.1', 8888);
}catch(Exception e){
	print e;
	sys.exit(0);
}

print(redstash.request('set', ['test', '123']));
print(redstash.request('get', ['test']));
print(redstash.request('incr', ['test', '1']));
print(redstash.request('decr', ['test', '1']));
print(redstash.request('scan', ['a', 'z', 10]));
print(redstash.request('rscan', ['z', 'a', 10]));
print(redstash.request('keys', ['a', 'z', 10]));
print(redstash.request('del', ['test']));
print(redstash.request('get', ['test']));
print "\n";
print(redstash.request('zset', ['test', 'a', 20]));
print(redstash.request('zget', ['test', 'a']));
print(redstash.request('zincr', ['test', 'a', 20]));
print(redstash.request('zdecr', ['test', 'a', 20]));
print(redstash.request('zscan', ['test', 'a', 0, 100, 10]));
print(redstash.request('zrscan', ['test', 'a', 100, 0, 10]));
print(redstash.request('zkeys', ['test', 'a', 0, 100, 10]));
print(redstash.request('zdel', ['test', 'a']));
print(redstash.request('zget', ['test', 'a']));
print "\n";
print(redstash.request('hset', ['test', 'a', 20]));
print(redstash.request('hget', ['test', 'a']));
print(redstash.request('hincr', ['test', 'a', 20]));
print(redstash.request('hdecr', ['test', 'a', 20]));
print(redstash.request('hscan', ['test', '0', 'z', 10]));
print(redstash.request('hrscan', ['test', 'z', '0', 10]));
print(redstash.request('hkeys', ['test', '0', 'z', 10]));
print(redstash.request('hdel', ['test', 'a']));
print(redstash.request('hget', ['test', 'a']));
print "\n";
