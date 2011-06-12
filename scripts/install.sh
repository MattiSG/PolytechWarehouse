#!/bin/bash

WWW_FOLDER='../www' # without trailing slash
ADMIN_PASSWORD_PLACEHOLDER='%admin%' # the default admin password in local.conf

source MSGShellUtils/ui.sh
source MSGShellUtils/stagedProcess.sh


cd $WWW_FOLDER

category 'SQLite PHP module…'
	try php -r 'phpinfo();' | grep sqlite
ok

category 'Configuration…'
	pass="admin"
	while [[ $pass = "admin" ]]
	do read -s -p "Please enter a non-trivial admin password: " pass
	done

	sed s:$ADMIN_PASSWORD_PLACEHOLDER:$pass: "config/local.conf" > "config/local.conf.php"
ok

category 'Permissions…'
	try  &&
	try chmod 777 database log uploads downloads &&
	try 
ok

cd - > /dev/null

echo
echo "To finalize the setup, you need to create the databases by accessing the URL '$boldon$cyanf http://server/warehouse/debug/setup.php $reset'"

end
