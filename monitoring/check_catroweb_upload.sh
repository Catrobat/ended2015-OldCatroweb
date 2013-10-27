#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script tests if a project is uploadable.

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

if [ -z "$HOST" ]
then
    usage
    exit 3
fi

catroweb_upload=$(curl --form upload=@/home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.zip --form projectTitle=Monitoring --form token=31df676f845b4ce9908f7a716a7bfa50 --form username=catroweb --form fileChecksum=d20c3ca0d3cd601582510fe6aca3ad0e --form visible=f $HOST/api/upload/upload.json 2>&1)

#check for success?
grep -q \"statusCode\":200 <<< "$catroweb_upload"

status=$?

#get project id from response "projectId":"322"
projectID=$(echo $catroweb_upload | sed -e 's/.*projectId":"//' | sed -e 's/\([0-9]\+\)\(.*\)/\1/')

  if [ $status -eq 0 ]
    then
      # if ! [ $projectID =~ ^[0-9]+$ ]
      # then
         echo "OK: $HOST accepts uploading projects. Uploaded projectID: $projectID"
      # else
      #   echo "UNKOWN: $HOST is in unkown state"
      #   status=3
      # fi
  elif [ $status -eq 1 ]
    then
      echo "WARNING: $HOST maybe rejects uploading projects"
  elif [ $status -eq 2 ]
    then
      echo "CRITICAL: $HOST rejects uploading projects"
  else
    echo "UNKNOWN: $HOST is in unkown state"
    status=3
  fi

exit $status
