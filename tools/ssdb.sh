#!/bin/sh
#
# chkconfig: 2345 64 36
# description: REDSTASH startup scripts
#
redstash_root=/usr/local/redstash
redstash_bin=$redstash_root/redstash-server
# each config file for one instance
# configs="/data/redstash_data/test/redstash.conf /data/redstash_data/test2/redstash.conf"
configs="/data/redstash_data/test/redstash.conf"

 
if [ -f /etc/rc.d/init.d/functions ]; then
	. /etc/rc.d/init.d/functions
fi
 
start() {
	for conf in $configs; do
		$redstash_bin $conf -s restart -d
	done
}
 
stop() {
	for conf in $configs; do
		$redstash_bin $conf -s stop -d
	done
}
 
# See how we were called.
case "$1" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        stop
        start
        ;;
    *)
        echo $"Usage: $0 {start|stop|restart}"
        ;;
esac
exit $RETVAL
