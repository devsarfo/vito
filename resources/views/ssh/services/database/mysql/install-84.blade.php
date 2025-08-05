#!/bin/bash

sudo rm -f /etc/apt/sources.list.d/mysql.list

sudo DEBIAN_FRONTEND=noninteractive apt-get update

sudo DEBIAN_FRONTEND=noninteractive apt-get install -y lsb-release

DEBIAN_FRONTEND=noninteractive wget https://dev.mysql.com/get/mysql-apt-config_0.8.34-1_all.deb

echo "mysql-apt-config mysql-apt-config/select-server select mysql-8.4-lts" | sudo debconf-set-selections
sudo DEBIAN_FRONTEND=noninteractive dpkg -i mysql-apt-config_0.8.34-1_all.deb

sudo DEBIAN_FRONTEND=noninteractive apt-get update

sudo DEBIAN_FRONTEND=noninteractive \
    apt-get -o Dpkg::Options::="--force-confdef" \
            -o Dpkg::Options::="--force-confold" \
    install -y mysql-server

sudo systemctl unmask mysql.service
sudo systemctl enable mysql
sudo systemctl start mysql

rm -f mysql-apt-config_0.8.34-1_all.deb

if ! sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket;"; then
    echo 'VITO_SSH_ERROR' && exit 1
fi

if ! sudo mysql -e "FLUSH PRIVILEGES"; then
    echo 'VITO_SSH_ERROR' && exit 1
fi
