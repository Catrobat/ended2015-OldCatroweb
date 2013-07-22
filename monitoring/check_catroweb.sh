#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script  tests if the host is alive and actual content is delivered.

OPTIONS:
   -h      host
EOF
}

HOST=
while getopts "h:" OPTION
do
    case $OPTION in
	h)
	    HOST=$OPTARG
	    ;;
    esac
done

if [ -z "$HOST" ]; then
    usage
    exit 3
fi

catroweb_login=$(curl $HOST/login 2>&1)

grep -q loginUsername <<< "$catroweb_login"

status=$?

#    if [ $status -ne 0 ]; then
#        echo "error with $1"
#    fi
#    return $status
exit $status

# 0: OK       AOK
# 1: Warning  some warning
# 2: Critical some error
# 3: Unknown  invalid commandline
exit 3