#!/bin/bash

server=unpriv@catroidwebtest.ist.tugraz.at
resource_location="/home/unpriv/Pootle-2.1.5/po"

keychain $HOME/.ssh/id_rsa_catroid
source $HOME/.keychain/$HOSTNAME-sh 

# package po folder
ssh ${server} "cd ${resource_location}; tar -zcvf  ~/pootle_backup.tar ."

# copy to local machine
scp ${server}:pootle_backup.tar pootle_backup.tar
retval=$?
if [ $retval -ne 0 ]; then
  scp backup.log unpriv@catroidwebtest.ist.tugraz.at:.
  ssh unpriv@catroidwebtest.ist.tugraz.at "mail -s 'CATROWEB: Backup failed - pootle language files!' 'webmaster@catroid.org' <backup.log"
  ssh unpriv@catroidwebtest.ist.tugraz.at "rm backup.log"
fi

# cleanup
ssh ${server} "rm pootle_backup.tar"

