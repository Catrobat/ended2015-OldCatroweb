#!/bin/bash

server=unpriv@catroidwebtest.ist.tugraz.at
resource_location="/home/unpriv/Pootle-2.1.5/po"

if [ $# -gt 0 ]; then
  backup_file=$1

  echo "================================================================================"
  echo "Server can be accessed with following command: ssh ${server}"
  echo "Pootle's po folder is located at: ${resource_location}"
  echo "Backup file which will be used: ${backup_file}"
  echo "================================================================================"
  echo ""
  read -p "Are this informations correct? [y/N]: " yn
  case $yn in
     [Yy] ) echo "Starting backup process...";;
     * ) exit;;
  esac

  # copy backup file to server
  scp ${backup_file} ${server}:.

  # restore resources folder
  ssh ${server} "rm -rf ${resource_location}"
  ssh ${server} "mkdir ${resource_location}"
  ssh ${server} "cd backup_tmp; tar -xvf resources.tar -C ${resource_location}"
  
  # cleanup
  ssh ${server} "rm -rf backup_tmp"

  echo "backup restored"
else
  echo "no restore file given"
fi

