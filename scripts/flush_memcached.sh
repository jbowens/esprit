#!/bin/sh
echo 'flush_all' | nc localhost 11211
echo 'Flushed memcached'
