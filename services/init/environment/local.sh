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


echo ""
echo " # check and install necessary packages..."
apt-get --assume-yes install ant ant-contrib apache2 cloc graphviz imagemagick libjpeg-progs linkchecker openjdk-7-jdk openjdk-7-jre-headless optipng php5 php5-gd php5-curl php-pear postgresql-9.1 postgresql-autodoc phppgadmin python-paramiko qrencode

pear channel-discover pear.phpunit.de
pear channel-discover components.ez.no
pear channel-discover pear.symfony-project.com
pear channel-discover pear.symfony.com
pear install --alldeps phpunit/PHPUnit phpunit/DbUnit phpunit/PHPUnit_Selenium phpunit/PHPUnit_Story XML_Serializer-0.20.2


echo ""
echo " # create catroweb home..."
ln -sf "${PWD}" /var/www/catroid


echo ""
echo " # configure apache..."
cp services/init/environment/VirtualHost.conf /etc/apache2/sites-available/catroweb
rm /etc/apache2/sites-enabled/*
ln -sf /etc/apache2/sites-available/catroweb /etc/apache2/sites-enabled/000-catroweb
ln -sf /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled	
service apache2 restart

echo ""
echo " # configure postgresql..."
sed -i "s/local   all             all                                     peer/local   all             all                                     trust/" /etc/postgresql/9.1/main/pg_hba.conf
service postgresql restart
su postgres services/init/environment/setup-db.sh

echo "127.0.0.1 catroid.local" >> /etc/hosts

touch /var/www/catroid/.setup

