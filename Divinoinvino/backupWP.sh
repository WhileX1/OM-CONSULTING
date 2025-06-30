#!/bin/bash
#@author        MagePsycho <magepsycho@gmail.com>
#@website       http://www.magepsycho.com
#@blog          http://www.blog.magepsycho.com
#@version       0.1.0
 
#/************************ EDIT VARIABLES ************************/
projectName=divino
backupDir=/home/$projectName/backups
#/************************ //EDIT VARIABLES **********************/
fileName=$projectName-$(date +"%Y-%m-%d")
host=$(grep DB_HOST "/home/$projectName/public_html/wp-config.php" |cut -d "'" -f 4)
username=$(grep DB_USER "/home/$projectName/public_html/wp-config.php" | cut -d "'" -f 4)
password=$(grep DB_PASSWORD "/home/$projectName/public_html/wp-config.php" | cut -d "'" -f 4)
dbName=$(grep DB_NAME "/home/$projectName/public_html/wp-config.php" |cut -d "'" -f 4)
    mysqldump -h "$host" -u "$username" -p"$password" "$dbName" | gzip > $backupDir/$fileName.sql.gz
    tar -zcf $backupDir/$fileName.tar.gz /home/$projectName/public_html/* /home/$projectName/public_html/.htaccess
   # rm -f $fileName.sql.gz
   # mkdir -p $backupDir;
   #  mv $fileName.sql.gz $backupDir
   #  mv $fileName.tar.gz $backupDir
   
find /home/$projectName/backups -type f -ctime +7 -exec rm  {} \;