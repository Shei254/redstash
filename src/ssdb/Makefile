include ../../build_config.mk

OBJS = redstash_impl.o iterator.o options.o \
	t_kv.o t_hash.o t_zset.o t_queue.o binlog.o ttl.o
LIBS = ../util/libutil.a


all: redstash.h ${OBJS}
	ar -cru ./libredstash.a ${OBJS}

redstash_impl.o: redstash.h redstash_impl.h redstash_impl.cpp
	${CXX} ${CFLAGS} -c redstash_impl.cpp
iterator.o: redstash.h iterator.h iterator.cpp
	${CXX} ${CFLAGS} -c iterator.cpp
options.o: redstash.h options.h options.cpp
	${CXX} ${CFLAGS} -c options.cpp
t_kv.o: redstash.h t_kv.h t_kv.cpp
	${CXX} ${CFLAGS} -c t_kv.cpp
t_hash.o: redstash.h t_hash.h t_hash.cpp
	${CXX} ${CFLAGS} -c t_hash.cpp
t_zset.o: redstash.h t_zset.h t_zset.cpp
	${CXX} ${CFLAGS} -c t_zset.cpp
t_queue.o: redstash.h t_queue.h t_queue.cpp
	${CXX} ${CFLAGS} -c t_queue.cpp
binlog.o: redstash.h binlog.h binlog.cpp
	${CXX} ${CFLAGS} -c binlog.cpp
ttl.o: redstash.h ttl.h ttl.cpp
	${CXX} ${CFLAGS} -c ttl.cpp

test:
	${CXX} -o test.out test.cpp ${OBJS} ${CFLAGS} ${LIBS} ${CLIBS}

clean:
	rm -f ${EXES} *.o *.exe *.a

