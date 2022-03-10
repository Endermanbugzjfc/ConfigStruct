# ConfigStruct

Type and shape system for arrays. Help write cleaner code when implementing configs for your PocketMine-MP plugin or
composer project.

It also generates more human-readable errors when something is wrong with the data. Encouraging and guiding the user (especially some PocketMine-MP server owners) to read the error and fix their mess.


![](https://i.imgflip.com/67yyc9.jpg)

https://github.com/Sandertv/Marshal is an alternative that supports lower versions of PHP. However, it is not as ~~bloat~~ feature-rich as this library.
# Preview
## Parse errors
```
2 errors in /Users/Shoghi/Documents/shog chips.yml
    1 errors in element "a"
        1 errors in index "0"
            1 errors in element "c"
                Element is array while it should be string
    1 errors in element "b"
        Element is null while it should be bool

 ```
 Notice there is a trailing line break.
<!-- TODO: In an uncaught PHP error message and PocketMine-MP server log. -->
# Developer guide
## Parsing data
```php
use Endermanbugzjfc\ConfigStruct\Parse;
```
```php
$context = Parse::object($object, $data);
$context->copyToObject($object, $dataFilePath);
```
`$dataFilePath` will be displayed in error messages if there is any.

The errors will be wrapped and thrown with a [ParseErrorsWrapper](https://github.com/Endermanbugzjfc/ConfigStruct/blob/master/ConfigStruct/src/Endermanbugzjfc/ConfigStruct/ParseErrorsWrapper.php) when calling `copyToObject()`. Although it is recommended to catch it, you can still ignore it. Because the errors can still be displayed well in a PHP uncaught error message.

You may use `Parse::objectByReflection()` if you don't have an object but instead, its ReflectionClass instance. And use `$context->copyToNewObject()` to copy the parsed data to a new object.
## Customising error message
### Changing the root header label
`/Users/Shoghi/Documents/shog chips.yml` is the root header label in the following errors tree:
```
2 errors in /Users/Shoghi/Documents/shog chips.yml
    1 errors in element "a"
        1 errors in index "0"
            1 errors in element "c"
                Element is array while it should be string
    1 errors in element "b"
        Element is null while it should be bool
 ```
 You can change it in the first argument of `ParseErrorsWrapper::regenerateErrorMessage()`:
```php
$parseErrorsWrapper->regenerateErrorMessage('C:\Windows\System32\ntoskrnl.exe');
```
### Changing the indentation
 You can change it in the second argument of `ParseErrorsWrapper::regenerateErrorMessage()`
 ### Filtering errors
 You can hide certain errors from the errors tree by filtering them out.
Apply an error filter with the third argument of `ParseErrorsWrapper::regenerateErrorMessage()`:
```php
$parseErrorsWrapper->regenerateErrorMessage(
    $parseErrorsWrapper->getRootHeaderLabel(),
    $parseErrorsWrapper->getIndentation(),
    fn (array $keys, BaseParseError $parseError) : bool => !$parseError instanceof TypeMismatchError
);
```
This filters out all the [TypeMismatchError](https://github.com/Endermanbugzjfc/ConfigStruct/blob/master/ConfigStruct/src/Endermanbugzjfc/ConfigStruct/ParseError/TypeMismatchError.php). Although `$parseError->getErrorsTree()` will still have them, they will not be shown in the error message.
### Print the updated error message
Simply throw the parse errors wrapper again. Or you may choose to `echo $parseErrorsWrapper->getMessage()`. By default, the error message has a trailing line break (`\n`). You can get an error message without the trailing line break (and other whitespaces) by calling `$parseErrorsWrapper->getMessageRtrim()` instead.