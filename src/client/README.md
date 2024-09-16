REDSTASH C++ API Documentation {#mainpage}
============

@author: [ideawu](http://www.ideawu.com/)

## Build the static library(libredstash-client.a)

Download the REDSTASH source code from [github](https://github.com/ideawu/redstash).

    make

The shell commands above will compile the C++ API codes, and generate a `libredstash-client.a` file.

## Sample code

	#include <stdio.h>
	#include <stdlib.h>
	#include <string>
	#include <vector>
	#include "REDSTASH_client.h"
	
	int main(int argc, char **argv){
		const char *ip = (argc >= 2)? argv[1] : "127.0.0.1";
		int port = (argc >= 3)? atoi(argv[2]) : 8888;
		
		redstash::Client *client = redstash::Client::connect(ip, port);
		if(client == NULL){
			printf("fail to connect to server!\n");
			return 0;
		}
		
		redstash::Status s;
		s = client->set("k", "hello redstash!");
		if(s.ok()){
			printf("k = hello redstash!\n");
		}else{
			printf("error!\n");
		}
		
		delete client;
		return 0;
	}

Save the codes above into a file named `hello-redstash.cpp`.

## Compile sample code

If you are under the directory `api/cpp`, compile it like this

	g++ -o hello-redstash hello-redstash.cpp libredstash-client.a
	./hello-redstash

Before you run `hello-redstash`, you have to start redstash-server with the default configuration. The output would be like

	k = hello redstash!

Connect to redstash-server with `redstash-cli`, to verify the key `k` is stored with the value "hello redstash!".

If your `hello-redstash.cpp` file is not under the directory `api/cpp`, you will compile it like this

	g++ -o hello-redstash -I<path of api/cpp> hello-redstash.cpp <path of api/cpp>/libredstash-client.a

