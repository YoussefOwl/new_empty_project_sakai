<!-- ----------------------------------------------------------------------- -->
<!--                                  Mysql                                  -->
<!-- ----------------------------------------------------------------------- -->

CREATE USER 'crm_depenses_user'@'localhost' IDENTIFIED BY 'Crm_depenses_password!123';
CREATE DATABASE IF NOT EXISTS `crm_depenses_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON crm_depenses_db.* TO 'crm_depenses_user'@'localhost';
FLUSH PRIVILEGES;

mysql -u root -p crm_depenses_db < /var/www/crm_depenses/api/database/backups/crm_depenses_db.sql
mysql -u root -p --init-command="SET FOREIGN_KEY_CHECKS=0" crm_depenses_db < /path/crm_depenses_db.sql

<!-- ----------------------------------------------------------------------- -->
<!--                               INSTALL PHP                               -->
<!-- ----------------------------------------------------------------------- -->
FOR Ubuntu 20.04 LTS OR GREATER (7)
sudo add-apt-repository ppa:ondrej/php

apt install php8.2
apt install php8.2-common php8.2-mysql php8.2-xml php8.2-xmlrpc php8.2-curl php8.2-gd php8.2-imagick php8.2-cli php8.2-dev php8.2-imap php8.2-mbstring php8.2-opcache php8.2-soap php8.2-zip php8.2-intl php8.2-fpm -y
systemctl start phpX.y-fpm.service
systemctl status phpX.y-fpm.service
sudo apt-get purge --auto-remove apache2
php -v

<!-- ----------------------------------------------------------------------- -->
<!--                             remove php X.y                              -->
<!-- ----------------------------------------------------------------------- -->

apt remove phpX.y-*
apt remove phpX.y-common phpX.y-json phpX.y-mysql phpX.y-xml phpX.y-xmlrpc phpX.y-curl phpX.y-gd phpX.y-imagick phpX.y-cli phpX.y-dev phpX.y-imap phpX.y-mbstring phpX.y-opcache phpX.y-soap phpX.y-zip phpX.y-intl phpX.y-fpm 
sudo apt-get autoremove
cd /etc/php 
rm -rf phpX.y

<!-- ----------------------------------------------------------------------- -->
<!--                             Remove composer                             -->
<!-- ----------------------------------------------------------------------- -->
which composer
sudo rm /usr/bin/composer

<!-- ----------------------------------------------------------------------- -->
<!--                       INSALL COMPOSER AND LARAVEL                       -->
<!-- ----------------------------------------------------------------------- -->
apt install curl
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
composer --version
composer global require laravel/installer
<!-- ----------------------------------------------------------------------- -->
<!--                                 LARAVEL                                 -->
<!-- ----------------------------------------------------------------------- -->

chown -R $USER:www-data storage
chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

php artisan storage:link
php artisan optimize:clear
composer install --optimize-autoloader --no-dev

# ---------------------------------------------------------------------------- #
#                                CREATE DATABASE                               #
# ---------------------------------------------------------------------------- #
CREATE DATABASE IF NOT EXISTS `your_db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# ---------------------------------------------------------------------------- #
#                               DEPLOY NGINX SITE                              #
# ---------------------------------------------------------------------------- #

ln -s /etc/nginx/sites-available/site_name /etc/nginx/sites-enabled/

# ---------------------------------------------------------------------------- #
#                               UNLINK NGINX SITE                              #
# ---------------------------------------------------------------------------- #

sudo unlink /etc/nginx/sites-enabled/site_name

# ---------------------------------------------------------------------------- #
#                                GET MYSQL USERS                               #
# ---------------------------------------------------------------------------- #

select host, user,plugin,authentication_string from mysql.user;

# ---------------------------------------------------------------------------- #
#                             RESET AUTO_INCREMENT                             #
# ---------------------------------------------------------------------------- #

ALTER TABLE TableName AUTO_INCREMENT = 1;
SHOW TABLE STATUS FROM `DatabaseName` WHERE `name` LIKE 'TableName' ; // afficher les propiétés d'une table

# ---------------------------------------------------------------------------- #
#                      CHECK IF FLIED VALUE IS DUPLICATED                      #
# ---------------------------------------------------------------------------- #

SELECT my_field, COUNT(*) count_my_field FROM commandes WHERE my_field IS NOT NULL GROUP BY my_field HAVING count_my_field > 1


# ---------------------------------------------------------------------------- #
#                                DELETE SQL USER                               #
# ---------------------------------------------------------------------------- #
use mysql;
SELECT User,Host,password FROM user;
DROP USER 'database_user'@'%';

# ---------------------------------------------------------------------------- #
#                               PERFORMANCE MYSQL                              #
# ---------------------------------------------------------------------------- #

To set the innodb_buffer_pool_size variable in MySQL 8.0.32-0ubuntu0.20.04.2, you can follow these steps:

Open the MySQL configuration file, which is usually located at /etc/mysql/mysql.conf.d/mysqld.cnf on Ubuntu systems.
Locate the [mysqld] section of the file, which contains server-specific options.
Add the following line to set the value of innodb_buffer_pool_size to the desired size in bytes (for example, 4 GB):

makefile
innodb_buffer_pool_size = 4G
query_cache_size = 1G
Save the file and exit.

Restart the MySQL service to apply the changes:

sudo systemctl restart mysql
That's it! The innodb_buffer_pool_size variable should now be set to the specified value.
You can verify the new setting by running the following command in the MySQL console:

SHOW VARIABLES LIKE 'innodb_buffer_pool_size';
This will display the current value of the innodb_buffer_pool_size variable.

apt-get install libmysqlclient-dev # python error

# ---------------------------------------------------------------------------- #
#                                 OTHER HLEPERS                                #
# ---------------------------------------------------------------------------- #

# Os version
cat /etc/os-release
# Available diks space
df -H 
# Memory
grep MemTotal /proc/meminfo
free -m
# MariadB
sudo apt update
sudo apt install mariadb-server mariadb-client
sudo mysql_secure_installation

# ---------------------------------------------------------------------------- #
#                              Secure root access                              #
# ---------------------------------------------------------------------------- #
# ---------------------------------- MariadB --------------------------------- #
GRANT ALL ON *.* TO 'root'@'localhost' IDENTIFIED BY 'azertyuiop!123' WITH GRANT OPTION;
# ------------------ mysql Ver 8.0.32-0ubuntu0.20.04.2 ----------------- #
GRANT ALL ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'your_mdp';
FLUSH PRIVILEGES;

# ---------------------------------------------------------------------------- #
#                                BACKUP DATABASE                               #
# ---------------------------------------------------------------------------- #

mysqldump -u root -p crm_depenses_db > /var/www/path/file_name.sql --skip-tz-utc

# ---------------------------------------------------------------------------- #
#                             RESET AUTO_INCREMENT                             #
# ---------------------------------------------------------------------------- #

ALTER TABLE table_name AUTO_INCREMENT = 1;
SHOW TABLE STATUS FROM `DatabaseName` WHERE `name` LIKE 'TableName' ; // afficher les propiétés d'une table

<!-- ----------------------------------------------------------------------- -->
<!--                              ZIP A FOLDER                               -->
<!-- ----------------------------------------------------------------------- -->

zip -r first_image_article.zip first_image_article

Sans oublier 

les variables pour l'upload sur php.ini

innodb_buffer_pool_size est une configuration de MySQL qui définit la taille du buffer pool InnoDB en octets. La valeur que vous avez mentionnée, 8589934592, est en octets. Pour convertir cette valeur en mégaoctets (Mo), divisez-la par (1024 * 1024).

8589934592 / (1024 * 1024) = 8192 Mo

# -- ----------------------------------------------------------------------- --#
# --                           Les erreurs python                            --#
# -- ----------------------------------------------------------------------- --#

apt-get update
apt-get install wget
apt-get install build-essential checkinstall
wget https://www.noip.com/client/linux/noip-duc-linux.tar.gz
tar xvf noip-duc-linux.tar.gz
cd noip-2.1.9-1
sudo make
sudo make install
sudo noip2
nano /etc/systemd/system/noip2.service
Y mettre le contenu suivant :
# -- ----------------------------------------------------------------------- --#
[Unit]
Description=No-IP Dynamic DNS Update Client
After=network.target

[Service]
Type=forking
ExecStart=/usr/local/bin/noip2

[Install]
WantedBy=multi-user.target

# -- ----------------------------------------------------------------------- --#
systemctl enable noip2
systemctl start noip2

ps aux | grep noip2
kill PID
systemctl start noip2
systemctl status noip2

<!-- ----------------------------------------------------------------------- -->
<!--                        Disable strict mode mysql                        -->
<!-- ----------------------------------------------------------------------- -->
To change the server's SQL mode to allow for more relaxed validation, you'll need to modify the MySQL server's configuration. Here's how to do it:

Open the MySQL configuration file with a text editor. The configuration file is typically located at /etc/mysql/my.cnf, /etc/mysql/mysql.conf.d/mysqld.cnf, or /etc/my.cnf. You can use an editor like nano to edit the file:
bash

sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
(Replace the file path with the correct path for your system)

Locate the [mysqld] section in the configuration file. Under this section, you'll find the sql_mode setting, which might look like this:
makefile

sql_mode=STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION
Remove the STRICT_TRANS_TABLES option from the sql_mode setting. If there are multiple modes, separate them with commas. For example, you can change the sql_mode setting to:
makefile

sql_mode=NO_ENGINE_SUBSTITUTION
Save the changes and exit the text editor. If you're using nano, press Ctrl + X, then press Y, and finally press Enter to save the changes and exit.

Restart the MySQL server for the changes to take effect:

bash

sudo systemctl restart mysql
Verify that the SQL mode has been updated. Log into the MySQL server and run the following command:
sql

SHOW VARIABLES LIKE 'sql_mode';
This should display the updated SQL mode without the STRICT_TRANS_TABLES option.

Now, the MySQL server should allow for more relaxed validation, and you should be able to insert datetime values with a broader range. However, keep in mind that by disabling strict mode, you might encounter unexpected behavior when inserting invalid data. It is generally recommended to use strict mode and ensure that the data being inserted is valid and properly formatted.

<!-- ----------------------------------------------------------------------- -->
<!--                              FIX TIME SYNC                              -->
<!-- ----------------------------------------------------------------------- -->
apt update
apt install ntp
timedatectl set-timezone Africa/Casablanca
systemctl restart ntp
<!-- ----------------------------------------------------------------------- -->
<!--                             Install python                              -->
<!-- ----------------------------------------------------------------------- -->

apt install python3 python3-pip 
ln -s /usr/bin/pip3 /usr/local/bin/pip

<!-- ----------------------------------------------------------------------- -->
<!--                           INSTALL SSH SERVER                            -->
<!-- ----------------------------------------------------------------------- -->

sudo apt install openssh-server
sudo apt install openssh-client
sudo systemctl enable ssh

<!-- ---------------- Coté client man in the middle attack ----------------- -->
ssh-keygen -R ip_server

# ---------------------------------------------------------------------------- #
#                                     HTTPS                                    #
# ---------------------------------------------------------------------------- #

apt-get update
sudo apt-get install certbot
apt-get install python3-certbot-nginx
nginx -t && nginx -s reload
sudo certbot --nginx -d depenses.promagasin.ma
sudo certbot --nginx -d api-depenses.promagasin.ma
crontab -e
0 12 * * * /usr/bin/certbot renew --quiet

<!-- ----------------------------------------------------------------------- -->
<!--               Résumé des Commandes Utilisées de la taille               -->
<!-- ----------------------------------------------------------------------- -->
Commande	Description
du -sh /path/to/directory	Taille totale du dossier en format lisible
du -h /path/to/directory	Taille de tous les fichiers et sous-dossiers
`du -ch /path/to/directory	grep total`

<!-- ----------------------------------------------------------------------- -->
<!--                             export via sssh                             -->
<!-- ----------------------------------------------------------------------- -->
Pour utiliser un fichier de configuration avec `mysqldump`, vous pouvez créer un fichier contenant vos informations d'identification et l'utiliser avec l'option `--defaults-extra-file`. Voici comment vous pouvez procéder :

### Étapes détaillées

1. **Créer un fichier de configuration sécurisé** :

   Créez un fichier texte (par exemple, `my.cnf`) avec les informations de connexion nécessaires :

   ```ini
   [client]
   user=root
   password=YourPassword
   ```

2. **Restreindre les permissions du fichier** :

   Assurez-vous que le fichier n'est lisible que par l'utilisateur actuel :

   ```bash
   chmod 600 /path/to/my.cnf
   ```

3. **Utiliser le fichier de configuration avec `mysqldump`** :

   Utilisez l'option `--defaults-extra-file` pour spécifier le fichier de configuration. Voici la commande complète pour effectuer le dump et le transférer vers un serveur distant :

   ```bash
   mysqldump --defaults-extra-file=/path/to/my.cnf database_name --skip-tz-utc | ssh user_or_root@ip_destination "cat > /var/www/database_name.sql"
   ```

### Commande détaillée

```bash
mysqldump --defaults-extra-file=/path/to/my.cnf database_name --skip-tz-utc | ssh user_or_root@ip_destination "cat > /var/www/database_name.sql"
```

### Explications

- **`--defaults-extra-file=/path/to/my.cnf`** : Spécifie le fichier de configuration contenant les informations d'identification MySQL.
- **`database_name`** : Le nom de la base de données à dumper.
- **`--skip-tz-utc`** : Option pour ignorer l'ajustement des heures au fuseau horaire UTC.
- **`| ssh user_or_root@ip_destination "cat > /var/www/database_name.sql"`** : Pipe la sortie de `mysqldump` directement vers le serveur distant en utilisant SSH.

### Vérification

Pour vérifier que le fichier a bien été transféré, vous pouvez vous connecter au serveur distant et vérifier la taille du fichier :

```bash
ssh user_or_root@ip_destination "ls -lh /var/www/database_name.sql"
```

### Résumé

1. **Créer et sécuriser le fichier de configuration** :

   ```ini
   [client]
   user=root
   password=YourPassword
   ```

   ```bash
   chmod 600 /path/to/my.cnf
   ```

2. **Exécuter la commande `mysqldump` avec SSH** :

   ```bash
   mysqldump --defaults-extra-file=/path/to/my.cnf database_name --skip-tz-utc | ssh user_or_root@ip_destination "cat > /var/www/database_name.sql"
   ```

3. **Vérifier le fichier sur le serveur distant** :

   ```bash
   ssh user_or_root@ip_destination "ls -lh /var/www/database_name.sql"
   ```

En suivant ces étapes, vous pouvez transférer un dump SQL directement vers un autre serveur de manière sécurisée et efficace.