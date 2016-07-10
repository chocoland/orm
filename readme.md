Choco ORM
========

Choco ORM is a small and simple ORM for php

* [How to install](#How-to-install)
* [How to use ](#How-to-use)
* [Simple explication](#Now-is-time-of-make-a-simple-explication)
* [Generate models and entities](#Generate-models-and-entities)
* [Import Choco ORM](#Import-Choco-ORM)
* [Basic CRUD](#Basic-CRUD)

How to install
---

* Edit your `composer.json`:

```json
{
	"repositories": [
        {
            "url": "https://github.com/chocoland/orm.git",
            "type": "vcs"
        }
    ],
    "require-dev": {
        "chocoland/orm": "dev-master"
    }
}
```

* Download the dependencies:

```bash
cd /path/to/folder
composer install
```

* Set environment Variables in linux:

```bash
PATH=$PATH:/path/to/folder/vendor/bin
```

* Set environment Variables in windows:

```bash
set PATH=%PATH%;C:\path\to\folder\vendor\bin
```

How to use 
---

In your project folder, create a folder named `config`, into config create others folders called `database` and `entities`

Now is time of make a simple explication
---

* Database structure:

```yaml
database_name:
    driver: pdo_driver
    host: hostname
    user: username
    pass: pass
```

* Entitie structure:

```yaml
database_name:
    entitie_name:
        property1: 
            type: int
            join: name_of_entitie_with_relationship_to_index
            required: true
        property2: 
            type: type_of_date_mysql
            required: true
        property3: 
            type: varchar
            size: 50
            required: true
```

* Relacionship structure example:

```yaml
database_name:
    name_of_entitie_with_relationship_to_index:
        property: 
            type: varchar
            size: 50
            required: true
            fill:
            	- hello
            	- world
            	- and
            	- welcome
            	- to
            	- Choco
            	- ORM
            	- tutorial
```

Generate models and entities
---

* Open a terminal a write:

```bash
cd /path/to/folder
orm drop
orm model
orm insert
```

Import Choco ORM
---

* Require section:

```php
// composer autoload
require_once 'vendor/autoload.php';

// session manager of choco
require_once 'vendor/chocoland/orm/session.php';

// basic include of orm
require_once 'vendor/chocoland/orm/orm.php';

// post method manager of choco
require_once 'vendor/chocoland/orm/post.php';

// autoloader generate by choco in command line
require_once 'config/autoloader.php';
```

* Namesmace section:

```php
// use POST and Session in this namespace
use \Choco\POST;
use \Choco\Session;
```

Basic CRUD:
---

```php
// instance the object
$entitie_name = new Entitie_name();

// property method with parameter is a setter
$entitie_name->property1('1');
$entitie_name->property2('1000');
$entitie_name->property3('hello');

// property method with parameter is a getter
var_dump($entitie_name->property1());
var_dump($entitie_name->property2());
var_dump($entitie_name->property3());

// create and save
$entitie_name->property1('1');
$entitie_name->property2('1000');
$entitie_name->property3('hello');
$entitie_name->save();

// read
var_dump($entitie_name->property1());
var_dump($entitie_name->property2());
var_dump($entitie_name->property3());

// update and save
$entitie_name->find('id = \'' . 1 . '\'');
$entitie_name->property1('1');
$entitie_name->property2('1000');
$entitie_name->property3('hello');
$entitie_name->save();

// sometimes find method return various keys and these changue the way of use the object
// method find without parameter, return all keys
$entitie_name->find();
var_dump($entitie_name->property1());
var_dump($entitie_name->property1()[0]);
var_dump($entitie_name->property1()[1]);
// is better see it as a array
$entitie_name->array();
// sometimes you need, know how many keys have the object
var_dump($entitie_name->length());
```