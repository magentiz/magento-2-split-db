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

## Support
If you have any issues, please [contact us](mailto:support@opentechiz.com)

## Need More Features?
Please contact us to get a quote
https://www.opentechiz.com/contact-us/

## License
The code is licensed under [Open Software License ("OSL") v. 3.0](http://opensource.org/licenses/osl-3.0.php).
