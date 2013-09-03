#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script tests if the host is alive, actual content is delivered and upload/download is functioning properly.

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

#-------------------------------------------------------------------------------
# is login reachable?
#-------------------------------------------------------------------------------

  SITE="login"

  catroweb_login=$(curl $HOST/$SITE 2>&1)

  grep -q loginUsername <<< "$catroweb_login"

  status_login=$?
  
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
# does upload work?
#-------------------------------------------------------------------------------
if [ $status_login -eq 0 ]
then
  catroweb_upload=$(curl --form upload=@testdata/test.zip --form projectTitle=Monitoring --form token=31df676f845b4ce9908f7a716a7bfa50 --form username=catroweb --form fileChecksum=14a5b75f6092726dbd5df8d12dc5aaf7 --form visible=f $HOST/api/upload/upload.json 2>&1)

#check for success?
  grep -q \"statusCode\":200 <<< "$catroweb_upload"

  status_upload=$?

#get project id from response "projectId":"322"
  PROJECTID=$(echo $catroweb_upload | sed -e 's/.*projectId":"//' | sed -e 's/\([0-9]\+\)\(.*\)/\1/')
else
  status_upload=1
  PROJECTID=0
fi
#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
# does download work?
#-------------------------------------------------------------------------------

if [ $status_upload -eq 0 ]
  then

  catroweb_project=$(curl -s $HOST/download/$PROJECTID.catrobat 2>&1)

  file_size=$(wc -c <<< "$catroweb_project")
else
  file_size=0
fi
#-------------------------------------------------------------------------------

status=3

if [ $status_login -eq 0 -a $status_upload -eq 0 -a $file_size -gt 1 ]
  then
    echo "OK: $HOST is online"
    status=0
else
   echo "CRITICAL: $HOST is down"
  status=2
fi

exit $status