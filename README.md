Cmsko - CMS for Kohana
======================

Content management base functionality for Kohana3.

There is development state. Some usage cases and design flow.

Creating helper instance
```php
$cms = Cmsko::factory(0);
```

Reading of data
```php
$cms->load('my_id')
```

or writing
```php
$cms->save('my_id', "Some data");
```

Functionality
-------------

* singleton with init method for use in init.php
* urls map (tree stuct) where you can load all cms nodes map to static var
* request mapping to cms node: url + language + placeholder id
* running from controller's before/after
* rendering custom views for cms fields
* dispatch results to views (inline cms fields)
* serialize map to driver stores: file, database, memcache, redis, mongo
* serialization of map to php file (caching) - must be implemented behind of drivers to inherit caching everywhere
* multiple cms instances support (for example for sync data from different driver-configured environments)
* CRUD operations
* test case with hmvc usage of multiple cms on few pages
* cms node can be redirect to external url
* support json interface for delegating in [Adminko](https://github.com/alexmav/adminko) or ext. api


Module structure
----------------

* REAMDE.md - this file
* init.php
* config/cmsko.php
* classes/Cmsko.php
* classes/Cmsko/<driver>.php
* classes/Kohana/Cmsko.php
* classes/Kohana/Cmsko/<driver>.php
* classes/Kohana/Model.php
* classes/Model/Contentnode.php
* classes/Model/Contentnode/Driver.php
* classes/Model/contentnode/<driver>.php

