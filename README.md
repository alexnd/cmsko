Cmsko - CMS for Kohana
======================

Content management base functionality

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


[urls map: url tree stuct]

[cms mapping: url, language, placeholder id]

[preprocessor: run filters and special things for cms from controller's before/after]

[render custom views for cms fields]

[dispatch results to views]

[inline cms fields on a page (+Adminko)]

[support json mode for delegating in adminko and ext. api]

[drivers: file, database, memcache, redis, mongo]

[cache route map in php files (not for heavy maps)]

[utilizes vendors: tinymce]

[ui into adminko]

[navigation trees]

[module structure]
REAMDE.md - this file
init.php
config/cmsko.php
classes/cmsko.php
classes/cmsko/<driver>.php
classes/kohana/cmsko.php
classes/kohana/cmsko/<driver>.php
classes/model/contentnode.php
classes/model/contentnode/file.php
classes/model/contentnode/orm.php
classes/controller

[functionality abstraction]
map request by uri to cms node
load all cms nodes map to static var
serialize map to driver store
serialize map to php file (caching) - must be implemented on top of drivers to inherit caching everywhere
crud for cms map
singleton with init method for use in init.php
multi-cms instances support (for example for sync data from different driver-configured environments)
test case with hmvc usage of multiple cms on few pages
cms node can be redirect to external url
custom attribute fields wrapped over cms nodes (orm or like linkage of external attributes)