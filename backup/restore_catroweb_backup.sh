#!/bin/bash

server=unpriv@catroidweb.ist.tugraz.at
resource_location="/var/www/catroid/resources"

keychain $HOME/.ssh/id_rsa_catroid
source $HOME/.keychain/$HOSTNAME-sh 

if [ $# -gt 0 ]; then
  backup_file=$1

  echo "================================================================================"
  echo "Server can be accessed with following command: ssh ${server}"
  echo "Catroid's resources folder is located at: ${resource_location}"
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

  # extract backup file
  ssh ${server} "rm -rf backup_tmp"
  ssh ${server} "mkdir -p backup_tmp"
  ssh ${server} "tar -xvf ${backup_file} -C backup_tmp"
  
  # restore database
  ssh ${server} "cd backup_tmp; tar -xvf sql.tar"

  ssh ${server} "dropdb catroboard"
  ssh ${server} "createdb catroboard"
  ssh ${server} "gzip -dc backup_tmp/catroboard.gz | pg_restore -d catroboard"
  
  ssh ${server} "dropdb catroweb"
  ssh ${server} "createdb catroweb"
  ssh ${server} "gzip -dc backup_tmp/catroweb.gz | pg_restore -d catroweb"
  
  ssh ${server} "dropdb catrowiki"
  ssh ${server} "createdb catrowiki"
  ssh ${server} "gzip -dc backup_tmp/catrowiki.gz | pg_restore -d catrowiki"
  
  # restore resources folder
  ssh ${server} "rm -rf ${resource_location}"
  ssh ${server} "mkdir ${resource_location}"
  ssh ${server} "cd backup_tmp; tar -xvf resources.tar -C ${resource_location}"
  ssh ${server} "chmod -R 0777 ${resource_location}"
  
  # cleanup
  ssh ${server} "rm -rf backup_tmp"

  echo "catroid.org was successfully restored!"
else
  echo "no restore file given"
fi

