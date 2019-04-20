#!/bin/bash
apt update
apt --assume-yes install apache2
apt --assume-yes install php libapache2-mod-php php-curl
apt --assume-yes install unzip
apache2ctl restart
cd /var/www/html
wget https://github.com/deniszhitnyakov/fbcosts/archive/master.zip
unzip master.zip
mv -R fbcosts-master fbcosts