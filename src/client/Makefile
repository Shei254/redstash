include ../../build_config.mk

all: lib
	${CXX} -o demo demo.cpp libredstash-client.a
	${CXX} -o hello-redstash hello-redstash.cpp libredstash-client.a

lib: REDSTASH_client.h REDSTASH_impl.h REDSTASH_impl.cpp
	${CXX} -I../ ${CFLAGS} -c REDSTASH_impl.cpp
	ar -cru libredstash-client.a\
		REDSTASH_impl.o\
		../util/bytes.o\
		../net/link.o ../net/link_addr.o
	cp REDSTASH_client.h libredstash-client.a ../../api/cpp

clean:
	rm -f demo hello-redstash *.a *.o

