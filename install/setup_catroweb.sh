#!/bin/bash
# tested on Ubuntu Lucid Lynx (10.04)

#the place where a new folder containing the files goes
WORKSPACE=~/Workspace/
#name of the new folder 
TARGET=catroweb

cd ${WORKSPACE}

echo ""
echo "check and install necessary packages..."
sudo add-apt-repository ppa:mercurial-ppa/releases
sudo apt-get update
sudo apt-get install eclipse apache2 php5 php5-gd php5-curl php-pear postgresql phppgadmin mercurial --yes --quiet

echo ""
echo "set java version"
sudo update-java-alternatives -s java-6-sun

echo ""
echo "configure mercurial, get googlecode fingerprint..."
fingerprint=$(openssl s_client -connect catroweb.catroid.googlecode.com:443 < /dev/null 2>/dev/null | openssl x509 -fingerprint -noout -in /dev/stdin)
fingerprint=${fingerprint#*=}
fingerprint="catroweb.catroid.googlecode.com = ${fingerprint}"

sudo chmod 0666 /etc/mercurial/hgrc
sudo echo "[hostfingerprints]" >> /etc/mercurial/hgrc
sudo echo $fingerprint >> /etc/mercurial/hgrc
sudo chmod 0644 /etc/mercurial/hgrc

echo ""
echo "configure apache..."
sudo ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled	
sudo sed -i "s/AllowOverride None/AllowOverride All/" /etc/apache2/sites-enabled/000-default
sudo sed -i "s/:80>/:80>\n        Alias \/${TARGET}\/wiki \"\/var\/www\/${TARGET}\/addons\/mediawiki\/index.php\"/" /etc/apache2/sites-enabled/000-default
sudo /etc/init.d/apache2 restart

echo ""
echo "configure pear..."
sudo pear install PEAR-1.9.2
sudo pear install XML_Serializer-0.20.2
sudo pear channel-discover pear.phpunit.de
sudo pear channel-discover components.ez.no
sudo pear channel-discover pear.symfony-project.com
sudo pear install phpunit/PHPUnit

echo ""
echo "get jsch..."
wget http://netcologne.dl.sourceforge.net/project/jsch/jsch.jar/0.1.44/jsch-0.1.44.jar
sudo mv jsch-0.1.44.jar /usr/share/ant/lib

echo ""
echo "clone repository..."
hg clone https://catroweb.catroid.googlecode.com/hg/ ${WORKSPACE}${TARGET}

echo ""
echo "apply changes to source files..."
chmod -R 0777 ${WORKSPACE}${TARGET}/resources
chmod -R 0777 ${WORKSPACE}${TARGET}/addons/board/cache

echo ""
echo "create postgres user..."
sudo -u postgres psql -d template1 -c "CREATE USER website WITH PASSWORD 'cat.roid.web';"
sudo -u postgres psql -d template1 -c "ALTER USER website CREATEDB;"

sudo -u postgres psql -d template1 -c "CREATE SCHEMA dbo;
CREATE OR REPLACE FUNCTION dbo.pg_kill_user_process(pid integer)
RETURNS boolean AS \$body\$
DECLARE
    result boolean;
BEGIN
    IF EXISTS (SELECT * FROM pg_catalog.pg_stat_activity
        WHERE usename IN (SELECT usename FROM pg_catalog.pg_stat_activity WHERE procpid = pg_backend_pid()) AND procpid = pid) THEN
            result := (SELECT pg_catalog.pg_terminate_backend(pid));
    ELSE
        result := false;
    END IF;
    RETURN result;
END;
\$body\$
    LANGUAGE plpgsql
    SECURITY DEFINER
    VOLATILE
    RETURNS NULL ON NULL INPUT
    SET search_path = pg_catalog;
REVOKE EXECUTE ON ALL FUNCTIONS IN SCHEMA dbo FROM PUBLIC;
GRANT USAGE ON SCHEMA dbo TO website;
GRANT EXECUTE ON FUNCTION dbo.pg_kill_user_process(pid integer) TO website;"



cd ${WORKSPACE}${TARGET}
sudo ln -s ${WORKSPACE}${TARGET} /var/www

echo ""
echo "create database and run tests..."
ant init-db 
ant run-phpunit-tests 

echo ""
echo "finished!"
firefox http://localhost/${TARGET}
