# ConfigStruct

Type and shape system for arrays. Help write cleaner code when implementing configs for your PocketMine-MP plugin or
composer project.

It also generates more human-readable errors when something is wrong with the data. Encouraging and guiding the user (especially some PocketMine-MP server owners) to read the error and fix their mess.


![](https://i.imgflip.com/67yyc9.jpg)

https://github.com/Sandertv/Marshal is an alternative that supports lower versions of PHP. However, it is not as ~~bloat~~ feature-rich as this library.
# Developer guide
## Parsing data
```php
use Endermanbugzjfc\ConfigStruct\Parse;
```
```php
$context = Parse::object(
	$object,
	$data
);
$context->copyToObject(
	$object,
	$dataFilePath
);
```
`$dataFilePath` will be displayed in error messages if there is any.

You may use `Parse::objectByReflection()` if you don't have an object but instead, its ReflectionClass instance. And use `$context->copyToNewObject()` to copy the parsed data to a new object.