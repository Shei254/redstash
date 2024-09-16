# REDSTASH - A Redis compatible NoSQL database stored on disk

[![Author](https://img.shields.io/badge/author-@ideawu-blue.svg?style=flat)](http://www.ideawu.net/) [![Platform](https://img.shields.io/badge/platform-Linux,%20BSD,%20OS%20X,%20Windows-green.svg?style=flat)](https://github.com/ideawu/redstash) [![NoSQL](https://img.shields.io/badge/db-NoSQL-pink.svg?tyle=flat)](https://github.com/ideawu/redstash) [![License](https://img.shields.io/badge/license-New%20BSD-yellow.svg?style=flat)](LICENSE)


REDSTASH is a high performace key-value(key-string, key-zset, key-hashmap) NoSQL database, __an alternative to Redis__.

REDSTASH is stable, production-ready and is widely used by many Internet companies including QIHU 360.

## Features

* LevelDB client-server support, written in C/C++
* Designed to store collection data
* Persistent key-value, key-zset, key-map('hashmap'), key-list storage
* Redis clients are supported
* Client API supports including C++, PHP, Python, Cpy, Java, nodejs, Ruby, Go([see all](http://redstash.io/docs/clients.html))
* Persistent queue service
* **Replication(master-slave), load balance**
* GUI administration tool([phpredstashadmin](https://github.com/redstash/phpredstashadmin))
* Built-in CLI nagios self-checks

## PHP client API example

```php
<?php
require_once('REDSTASH.php');
$redstash = new SimpleREDSTASH('127.0.0.1', 8888);
$resp = $redstash->set('key', '123');
$resp = $redstash->get('key');
echo $resp; // output: 123
```

[More...](http://redstash.io/docs/php/)


## Who's using REDSTASH?

[REDSTASH users...](http://redstash.io/docs/users.html)


## Documentation

* [View online](http://redstash.io/docs/)
* [Contribute to REDSTASH documentation project](https://github.com/ideawu/redstash-docs)

## Compile and Install

```sh
$ wget --no-check-certificate https://github.com/ideawu/redstash/archive/master.zip
$ unzip master
$ cd redstash-master
$ make
$ #optional, install redstash in /usr/local/redstash
$ sudo make install

# start master
$ ./redstash-server redstash.conf

# or start as daemon
$ ./redstash-server -d redstash.conf

# redstash command line
$ ./tools/redstash-cli -p 8888

# stop redstash-server
$ ./redstash-server redstash.conf -s stop
 # for older version
$ kill `cat ./var/redstash.pid`
```

See [Compile and Install wiki](http://redstash.io/docs/install.html)

## Performance

### Typical performance

Total 1000 requests.

```
writeseq  :    0.546 ms/op      178.7 MB/s
writerand :    0.519 ms/op      188.1 MB/s
readseq   :    0.304 ms/op      321.6 MB/s
readrand  :    0.310 ms/op      315.0 MB/s
```

### REDSTASH vs Redis

![Benchmark vs Redis](http://redstash.io/redstash-vs-redis.png?github)

[View full REDSTASH vs Redis benchmark charts...](http://redstash.io/)

### Concurrency benchmark

```
========== set ==========
qps: 44251, time: 0.226 s
========== get ==========
qps: 55541, time: 0.180 s
========== del ==========
qps: 46080, time: 0.217 s
========== hset ==========
qps: 42338, time: 0.236 s
========== hget ==========
qps: 55601, time: 0.180 s
========== hdel ==========
qps: 46529, time: 0.215 s
========== zset ==========
qps: 37381, time: 0.268 s
========== zget ==========
qps: 41455, time: 0.241 s
========== zdel ==========
qps: 38792, time: 0.258 s
```

Run on a 2013 MacBook Pro 13 inch with Retina display.

## Architecture

![redstash architecture](http://redstash.io/redstash.png)

## Windows executable

Download redstash-server.exe from here: https://github.com/ideawu/redstash-bin


## REDSTASH library for iOS

	make ios
	# ls ios/
	include/ libleveldb-ios.a libsnappy-ios.a libredstash-ios.a libutil-ios.a

Drag the static libraies files into your iOS project. Then add `ios/include` to your iOS project's __Header Search Paths__, which is set in __Build Settings__.

## Links

* [Author's homepage](http://www.ideawu.com/blog/)
* [Cpy Scripting Language](https://github.com/ideawu/cpy)
* [Google LevelDB](https://code.google.com/p/leveldb/)
* [Lua redstash client driver for the ngx_lua](https://github.com/LazyZhu/lua-resty-redstash)
* [Yet another redstash client for Python](https://github.com/ifduyue/pyredstash)
* [REDSTASH 中文文档](http://www.ideawu.net/blog/category/redstash)

## Changes made to LevelDB

See [Changes-Made-to-LevelDB wiki](https://github.com/ideawu/redstash/wiki/Changes-Made-to-LevelDB)

## LICENSE

REDSTASH is licensed under [New BSD License](http://opensource.org/licenses/BSD-3-Clause), a very flexible license to use.

## Authors

@ideawu(wuzuyang1@gmail.com)

## Thanks

* 刘建辉, liujianhui@gongchang.com
* wendal(陈镇铖), wendal1985@gmail.com, http://wendal.net 
