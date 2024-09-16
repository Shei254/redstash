PREFIX=/usr/local/redstash

$(shell sh build.sh 1>&2)
include build_config.mk

all:
	mkdir -p var var_slave
	chmod u+x "${LEVELDB_PATH}/build_detect_platform"
	chmod u+x deps/cpy/cpy
	chmod u+x tools/redstash-cli
	cd "${LEVELDB_PATH}"; ${MAKE}
	cd src/util; ${MAKE}
	cd src/net; ${MAKE}
	cd src/client; ${MAKE}
	cd src/redstash; ${MAKE}
	cd src; ${MAKE}
	cd tools; ${MAKE}

.PHONY: ios
	
ios:
	cd "${LEVELDB_PATH}"; make clean; CXXFLAGS=-stdlib=libc++ ${MAKE} PLATFORM=IOS
	cd "${SNAPPY_PATH}"; make clean; make -f Makefile-ios
	mkdir -p ios
	mv ${LEVELDB_PATH}/out-ios-universal/libleveldb.a ios/libleveldb-ios.a
	mv ${SNAPPY_PATH}/libsnappy-ios.a ios/
	cd src/util; make clean; ${MAKE} -f Makefile-ios
	cd src/redstash; make clean; ${MAKE} -f Makefile-ios

install:
	mkdir -p ${PREFIX}
	mkdir -p ${PREFIX}/_cpy_
	mkdir -p ${PREFIX}/deps
	mkdir -p ${PREFIX}/var
	mkdir -p ${PREFIX}/var_slave
	cp -f redstash-server redstash.conf redstash_slave.conf ${PREFIX}
	cp -rf api ${PREFIX}
	cp -rf \
		tools/redstash-bench \
		tools/redstash-cli tools/redstash_cli \
		tools/redstash-cli.cpy tools/redstash-dump \
		tools/redstash-repair \
		${PREFIX}
	cp -rf deps/cpy ${PREFIX}/deps
	chmod 755 ${PREFIX}
	rm -f ${PREFIX}/Makefile

clean:
	rm -f *.exe.stackdump
	rm -rf api/cpy/_cpy_
	rm -f api/python/REDSTASH.pyc
	rm -rf db_test
	cd deps/cpy; ${MAKE} clean
	cd src/util; ${MAKE} clean
	cd src/redstash; ${MAKE} clean
	cd src/net; ${MAKE} clean
	cd src; ${MAKE} clean
	cd tools; ${MAKE} clean

clean_all: clean
	cd "${LEVELDB_PATH}"; ${MAKE} clean
	rm -f ${JEMALLOC_PATH}/Makefile
	cd "${SNAPPY_PATH}"; ${MAKE} clean
	rm -f ${SNAPPY_PATH}/Makefile
	
