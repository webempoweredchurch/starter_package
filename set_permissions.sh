#!/bin/sh
while true; do
	echo "This script will set the permissions of several directories to 777, making them readable, writeable, and executable by anyone else on this system."
	echo -n "Are you sure you want to set the permissions this way (Y/N)?  "
	read yn
	case $yn in
	  y* | Y* ) chmod -R 777 typo3conf; chmod -R 777 typo3temp; chmod -R 777 uploads; chmod -R 777 fileadmin; break ;;
	  n* | N* ) exit ;;
	esac
done