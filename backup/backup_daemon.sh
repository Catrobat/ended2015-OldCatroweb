#!/bin/bash
####################################
#
# Backup to NFS mount script with
# grandfather-father-son rotation.
#
####################################

# Where to backup to.
backup_folder=$HOME/backup
dest="$backup_folder/data"

cd $backup_folder
mkdir -p $dest

# Setup variables for the archive filename.
day=$(date +%A)

# Find which week of the month 1-4 it is.
day_num=$(date +%d)
if (( $day_num <= 7 )); then
        week_file="week1.tgz"
elif (( $day_num > 7 && $day_num <= 14 )); then
        week_file="week2.tgz"
elif (( $day_num > 14 && $day_num <= 21 )); then
        week_file="week3.tgz"
elif (( $day_num > 21 && $day_num < 32 )); then
        week_file="week4.tgz"
fi

# Find if the Month is odd or even.
month_num=$(date +%m)
month=$(expr $month_num % 2)
if [ $month -eq 0 ]; then
        month_file="month2.tgz"
else
        month_file="month1.tgz"
fi

# Create archive filename.
if [ $day_num == 1 ]; then
	archive_file=$month_file
elif [ $day != "Saturday" ]; then
        archive_file="$day.tgz"
else 
	archive_file=$week_file
fi


################ backup catroweb
# Print start status message.
echo "Backing up catroid.org to $dest/catroweb-$archive_file"
date
echo

# Backup the files
./generate_catroweb_backup.sh
mv catroweb_backup.tar $dest/catroweb-$archive_file
################ backup catroweb done


################ backup catroweb
# Print start status message.
echo "Backing up pootle to $dest/pootle-$archive_file"
date
echo

# Backup the files
./generate_pootle_backup.sh
mv pootle_backup.tar $dest/pootle-$archive_file
################ backup catroweb done


# Print end status message.
echo
echo "Backup finished"
date


# Long listing of files in $dest to check file sizes.
ls -lh $dest/

