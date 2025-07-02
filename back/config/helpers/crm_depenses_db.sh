#!/bin/bash

# Database credentials
user="crm_depenses_user"
password="Crm_depenses_password!123"
host="localhost"
db_name="crm_depenses_db"

# Other options
backup_path="/var/www/crm_depenses/crm_depenses_dbs"
date=$(date +"%d-%m-%Y-%H-%M")
folder_name=$(date +"%m-%Y")

# Create backup directory if it does not exists
mkdir -p $backup_path/$folder_name

# Change permissions of the folder
chmod 755 $backup_path/$folder_name

# Check if backup file already exists
if [ ! -f $backup_path/$folder_name/$db_name-$date.sql ]; then
    # Dump database into SQL file
    mysqldump --user=$user --password=$password --host=$host --skip-tz-utc $db_name > $backup_path/$folder_name/$db_name-$date.sql
    # Change permissions of the file
    chmod 755 $backup_path/$folder_name/$db_name-$date.sql
fi

# ---------------------------------- Execute --------------------------------- #
# chmod +x crm_depenses_db.sh
# ./crm_depenses_db.sh
# ------------ Make the script as a cron job executable every 3 AM ----------- #
# crontab -e
# 0 3 * * * /var/www/crm_depenses/crm_depenses_db.sh