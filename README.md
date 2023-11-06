# Magentiz SplitDb by Open Techiz

Magentiz_SplitDb Extension, splitdb for magento 2

## Requirements
  * Magento Community Edition 2.3.x-2.4.x or Magento Enterprise Edition 2.3.x-2.4.x
  * Exec function needs to be enabled in PHP settings.

## Installation Method 1 - Installing via composer
  * Open command line
  * Using command "cd" navigate to your magento2 root directory
  * Run command: composer require magentiz/splitdb

## Installation Method 2 - Installing using archive
  * Download [ZIP Archive](link)
  * Extract files
  * In your Magento 2 root directory create folder app/code/Magentiz/SplitDb
  * Copy files and folders from archive to that folder
  * In command line, using "cd", navigate to your Magento 2 root directory
  * Run commands:
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Setup Testing Environment Guidelines

### Step 1: Prepare Magento Project:

First, you need to set up a Magento site running locally. Refer to this guide: [Docker Magento](https://github.com/markshust/docker-magento)

### Step 2: Install MySQL Replication Master-Slave:

Refer to [Docker MariaDb Replication](https://github.com/vtearit/docker-mariadb-replication), to integrate MySQL Replication into the website you just installed following step 1, you can follow the guide:
  * Copy the [db_slave.env file](https://github.com/vtearit/docker-mariadb-replication/blob/master/env/db_slave.env) from the env directory of docker-mariadb-replication and paste it into the env directory of the Magento project. Verify that the information in the file corresponds to the project's configuration.
  * Copy the [replication](https://github.com/vtearit/docker-mariadb-replication/tree/master/replication) folder and paste it into the root directory of the project.
  * Copy the services **```db```** and **```db_slave```** from the [docker-compose.yml](https://github.com/vtearit/docker-mariadb-replication/blob/master/docker-compose.yml) file to replace the **```db```** service in the compose.yaml file in the project's root directory. Also, add the **```dbslavedata```** volume to the list of volumes at the end of the compose.yaml file. The purpose of this is to create an additional Docker container for the MariaDB slave.
  * From root directory, Run ```bin/restart``` and follow the steps to switch to MySQL replication as described in [Docker MariaDb Replication](https://github.com/vtearit/docker-mariadb-replication): *access master-run query* and *access slave-run query*. After that, you can verify by adding a record to the database.
  * After successfully switching the website to use MySQL Replication, run ```bin/magento setup:uninstall``` to uninstall magento then run ```bin/setup-install``` to reinstall. At this point, since you have set up MySQL Replication, the data will also be updated on the slave.

### Step 3: Install Magentiz_SplitDb extension using Composer or the zip file as mentioned above.


## Support
If you have any issues, please [contact us](mailto:support@opentechiz.com)

## Need More Features?
Please contact us to get a quote
https://www.opentechiz.com/contact-us/

## License
The code is licensed under [Open Software License ("OSL") v. 3.0](http://opensource.org/licenses/osl-3.0.php).
