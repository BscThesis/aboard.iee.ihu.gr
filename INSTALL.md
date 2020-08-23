# This doc contains some basic info for deploying this app in a dev environment.

## Highly untested, but should work!

```
sudo su

apt update

apt install nginx php php-curl php-common php-cli php-mysql php-mbstring php-fpm php-xml php-zip php-ldap mariadb-server composer npm nodejs zip unzip git

mysql_secure_installation

mkdir /var/www/html/aboard

git clone \$REPO /var/www/html/aboard

mysql -u root -p
CREATE DATABASE laravel;
GRANT ALL ON laravel.\* to 'laravel'@'localhost' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
quit

cd /var/www/html/aboard

cp .env.example .env

php artisan key:generate
```

example nginx config => https://laravel.com/docs/6.x/deployment#nginx

You will also need a test LDAP server or a VPN connection to IEE. The one in IEE is in a private ip.

Update the values in .env with the required ones (db, ldap config etc)

```
composer install

npm install

npm run dev
```

visit http://IP and you should be good to go
