## Php class

The php class offers methods

* to fetch data from the api and
* to return meta infos

You can have a look to an example in the folder `php-example` how to code a simple public monitor.

![PHP example page](./../images/php-example-01.png)

### Requirements

* PHP 8 (up to PHP 8.5)
* php_curl, php_json

### Installation

Copy the file `appmonitorapi.class.php` into your project, eg, below `./vendor/`.

If you want to use git:

```txt
# get the repo
cd ~/tmp
git clone https://github.com/iml-it/appmonitor-api-client/

# copy the class file
cd [webroot]/vendor
mkdir appmonitor-api-client
cd appmonitor-api-client
cp ~/tmp/appmonitor-api-client/php-class/appmonitorapi.class.php .
```
