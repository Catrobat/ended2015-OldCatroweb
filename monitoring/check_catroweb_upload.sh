#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script tests if a project is uploadable.

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

catroweb_upload=$(curl --form upload=@/home/catroweb/Workspace/catroweb/tests/phpunit/api/testdata/test.zip --form projectTitle=Monitoring --form token=31df676f845b4ce9908f7a716a7bfa50 --form username=catroweb --form fileChecksum=14a5b75f6092726dbd5df8d12dc5aaf7 $HOST/api/upload/upload.json 2>&1)

#check for success?
grep \"statusCode\":200 <<< "$catroweb_upload"
#grep -q  statusCode <<< "$catroweb_upload"


#get project id from response "projectId":"322"

#set project invisible; fix that; file should be invisible already at upload!
#--------------------
#curl --user admin:cat.roid.web $HOST/admin/tools/toggleProjects --form projectId=322 --form toggle=visible
#catroweb_project_invisible=$(curl --user admin:cat.roid.web $HOST/admin/tools/toggleProjects --form projectId=322 --form toggle=invisible 2>&1)