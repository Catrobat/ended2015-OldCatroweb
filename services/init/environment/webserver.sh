#!/bin/bash

# Make sure only root can run our script
if [ "$(id -u)" != "0" ]; then
  echo "This script must be run as root" 1>&2
  exit 1
fi

if [ -f /var/www/catroid/.setup ]; then
  echo "This host was already set up to run Catroweb." 1>&2
  echo "Delete /var/www/catroid/.setup to run the setup process again"
  exit 1
fi

if [ $# -lt 1 ]; then
  echo "Please specify a user who should be the owner of Catrowebs root directory." 1>&2
  exit 1
fi


echo ""
echo " # check and install necessary packages..."
backport_source="deb http://backports.debian.org/debian-backports squeeze-backports main"
grep -q "${backport_source}" /etc/apt/sources.list || echo "${backport_source}" >> /etc/apt/sources.list
apt-get update

apt-get --assume-yes install apache2 graphviz imagemagick openjdk-6-jre-headless php5 php5-gd php5-curl php5-mcrypt php-xml-serializer unzip qrencode
apt-get --assume-yes --target-release squeeze-backports install postgresql-9.1 phppgadmin


echo ""
echo " # create catroweb home..."
mkdir -p /var/www/catroid
chown "$1:$1" /var/www/catroid


echo ""
echo " # configure apache..."
mv VirtualHost.conf /etc/apache2/sites-enabled/000-default
ln -sf /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled	
service apache2 restart


echo ""
echo " # configure postgresql..."
sed -i "s/local   all             all                                     peer/local   all             all                                     trust/" /etc/postgresql/9.1/main/pg_hba.conf
service postgresql restart
su postgres setup-db.sh

touch /var/www/catroid/.setup

rm setup.sh
rm setup-db.sh
