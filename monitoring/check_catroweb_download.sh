#!/bin/bash

usage()
{
cat << EOF
usage: $0 options

This script tests if a project file is downloadable.

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

#                                      projectId           Projectname
#catroweb_project=$(curl $HOST/download/880.catrobat?fname=compass+life+long+kindergarten 2>&1)
catroweb_project=$(curl $HOST/download/322.catrobat?fname=Monitoring 2>&1)

#cat <<< "$catroweb_project"
#wc -l <<< "$catroweb_project"
#md5sum <<< "$catroweb_project"

#delete file? it's invisible! at least it should be! ;-)