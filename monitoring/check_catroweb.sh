#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script tests if the host is alive and actual content is delivered.

Return-Values:
0: OK       Host is online
1: Warning  Host maybe down
2: Critical Host is down
3: Unknown  e.g. invalid commandline

OPTIONS:
   -h      host (e.g. https://pocketcode.org)
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


SITE="login"

  catroweb_login=$(curl $HOST/$SITE 2>&1)

  grep -q loginUsername <<< "$catroweb_login"

  status=$?

    if [ $status -eq 0 ]
    then
        echo "OK: $HOST is online"
    elif [ $status -eq 1 ]
    then
      echo "WARNING: $HOST maybe down"
    elif [ $status -eq 2 ]
    then
      echo "CRITICAL: $HOST is down"
    else
      echo "UNKOWN: $HOST is in unkown state"
      status=3
    fi

exit $status