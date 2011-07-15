#!/bin/bash

server=unpriv@catroidweb.ist.tugraz.at
resource_location="/var/www/catroid/resources"

keychain $HOME/.ssh/id_rsa_catroid
source $HOME/.keychain/$HOSTNAME-sh 

# dump sql databases
ssh ${server} "pg_dump -Fc -b catroboard | gzip -c9 > catroboard.gz"
ssh ${server} "pg_dump -Fc -b catroweb | gzip -c9 > catroweb.gz"
ssh ${server} "pg_dump -Fc -b catrowiki | gzip -c9 > catrowiki.gz"

# package sql dumps
ssh ${server} "tar -cjf sql.tar catroboard.gz catroweb.gz catrowiki.gz"

# package resources folder
ssh ${server} "cd ${resource_location}; tar -zcvf  ~/resources.tar ."

# package backup
ssh ${server} "tar -cjf catroweb_backup.tar sql.tar resources.tar"

# copy to local machine
scp ${server}:catroweb_backup.tar catroweb_backup.tar
retval=$?
if [ $retval -ne 0 ]; then
  scp backup.log unpriv@catroidwebtest.ist.tugraz.at:.
  ssh unpriv@catroidwebtest.ist.tugraz.at "mail -s 'CATROWEB: Backup failed - catroid.org!' 'webmaster@catroid.org' <backup.log"
  ssh unpriv@catroidwebtest.ist.tugraz.at "rm backup.log"
fi

# cleanup
ssh ${server} "rm catroboard.gz catroweb.gz catrowiki.gz sql.tar resources.tar catroweb_backup.tar"

