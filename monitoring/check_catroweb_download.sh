#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script tests if a project file is downloadable.

Return-Values:
0: OK       Host is online
1: Warning  Host maybe down
2: Critical Host is down
3: Unknown  e.g. invalid commandline

OPTIONS:
   -h      host (e.g. https://pocketcode.org)
   -p      projectID (e.g. 322)
EOF
}

HOST=
while getopts "h:p:" OPTION
do
    case $OPTION in
	h)
	    HOST=$OPTARG
	    ;;
        p)
	    PROJECTID=$OPTARG
	    ;;
    esac
done

if [ -z "$HOST" ]; then
    usage
    exit 3
fi

if [ -z "$PROJECTID" ]; then
    usage
    exit 3
fi

catroweb_project=$(curl -s $HOST/download/$PROJECTID.catrobat 2>&1)

file_size=$(wc -c <<< "$catroweb_project")

  if [ $file_size -gt 1 ]
    then
      echo "OK: $HOST supports downloading projects"
      status=0
  else
      echo "CRITICAL: $HOST doesn't support downloading projects"
      status=2
  fi

exit $status