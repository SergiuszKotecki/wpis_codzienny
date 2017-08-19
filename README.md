Requirement
-----------
- PHP >= 5.4
- MySQL >= 5.6
- mod_rewrite enabled
- GD library
- CURL library

Installation
----------
Get newest version of composer
~~~
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
~~~
Add composer asset plugin
~~~
composer global require "fxp/composer-asset-plugin:*"
~~~
Go to application dir and install repositories
~~~
cd path/to/appllication
composer install
~~~
Set dirs permission
~~~
cd path/to/appllication
chmod -r 777 ./runtime
chmod -r 777 ../public/assets
~~~
Dump SQL file
~~~
/path/to/application/dump.sql
~~~

Configuration
-------------
Set MySQL credentials in
~~~
/application/config/db.php
~~~

Set wykop API credentials in
~~~
/application/config/params.php
~~~

Set crontab to run command every minute
~~~
crontab -e
*/1 * * * * php /path/to/application/yii wpis
~~~

Test application
----------------
Create Thread and Thread entry as described in http://{your domain}/help:
- create new Thread
    - set `Time of dispatch` to current one
- create new Thread entry

Run command from console
~~~
cd /path/to/application
php yii wpis
~~~~
Remove newly created record from database table `threads_rows_cron`

TODO
----
- add comments
- add moderators
- templates
- followers list
- remove post by app

CONTACT
-------
PM me on https://www.wykop.pl/ludzie/AlvarezCasarez/