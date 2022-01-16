# ConfigStruct

PocketMine-MP 4 virion. Allows you to make structured config files much easier, in a struct-like class!

# Aborted Features
**(Feel free to make a pull request if you really want them to be implemented!)**

## Initialization of Group

It is quite useless and complicate to implement. In PHPStorm, there isn't an option for overriding properties in the
Generate action.

My opinion, it is better to initialize these kinds of special properties in a constructor rather than writing them in
this weird way.

## The AutoInitializeChildStruct attribute

This attribute was used for making a child-struct property to automatically initialize and specify one type of struct in
a union-types property.

But now there is the "Initialize" class which do all these automatically. However, union-types are ignored.



