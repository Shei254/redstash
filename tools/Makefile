include ../build_config.mk

OBJS += ../src/net/link.o ../src/net/link_addr.o ../src/net/fde.o \
	../src/util/log.o ../src/util/bytes.o
CFLAGS += -I../src
EXES = redstash-bench redstash-dump redstash-repair leveldb-import

all: redstash-bench.o redstash-dump.o redstash-repair.o leveldb-import.o redstash-migrate.o
	${CXX} -o redstash-bench redstash-bench.o ${OBJS} ${UTIL_OBJS} ${CLIBS}
	${CXX} -o redstash-dump redstash-dump.o ${OBJS} ${UTIL_OBJS} ${CLIBS}
	${CXX} -o redstash-repair redstash-repair.o ${OBJS} ${UTIL_OBJS} ${CLIBS}
	${CXX} -o leveldb-import leveldb-import.o ${OBJS} ${UTIL_OBJS} ${CLIBS}
	${CXX} -o redstash-migrate redstash-migrate.o ../api/cpp/libredstash-client.a ../src/util/libutil.a

redstash-migrate.o: redstash-migrate.cpp
	${CXX} ${CFLAGS} -I../api/cpp -c redstash-migrate.cpp
redstash-bench.o: redstash-bench.cpp
	${CXX} ${CFLAGS} -c redstash-bench.cpp
redstash-dump.o: redstash-dump.cpp
	${CXX} ${CFLAGS} -c redstash-dump.cpp
redstash-repair.o: redstash-repair.cpp
	${CXX} ${CFLAGS} -c redstash-repair.cpp
leveldb-import.o: leveldb-import.cpp
	${CXX} ${CFLAGS} -c leveldb-import.cpp

clean:
	rm -f *.exe *.exe.stackdump *.o ${EXES}
	rm -rf _cpy_

