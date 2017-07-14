sudo mkdir assets/json/tickets
sudo chmod 777 assets/json/tickets -R
sudo mkdir errors
sudo touch errors/error_log
sudo chmod 777 errors -R
sudo apt-get install curl php7.0-curl
DIR=${PWD##*/}
DIRPATH=$PWD
sudo touch "/etc/apache2/sites-available/$DIR.conf"

echo '<VirtualHost *:80>' | sudo tee "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo "ServerName '$DIR'" | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo "ServerAlias 'www.$DIR'" | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo "DocumentRoot '$DIRPATH'" | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null

echo "ErrorLog '$DIRPATH/errors/error_log'" | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'DirectoryIndex index.php' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo '<Directory />' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'Options FollowSymLinks' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'AllowOverride All' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo '</Directory>' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo "<Directory '$DIRPATH'>" | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'Options Indexes FollowSymLinks MultiViews' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'AllowOverride All' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'Order allow,deny' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo 'allow from all' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo '</Directory>' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null
echo '</VirtualHost>' | sudo tee --append "/etc/apache2/sites-available/$DIR.conf" > /dev/null

echo "127.0.0.1	$DIR" | sudo tee --append /etc/hosts > /dev/null

sudo a2ensite "$DIR"

sudo service apache2 restart
