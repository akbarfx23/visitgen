#!/bin/bash
apt update -y 
apt install screen php php-curl php-mbstring php-xml php-zip tor torsocks -y
mv torrc /etc/tor/torrc
mv torsocks.conf /etc/tor/torsocks.conf
sytemctl enable tor
service tor start
screen -admS visitgen php visitgen.php