# ConfigStruct

PocketMine-MP 4 virion. Allows you to make structured config files much easier, in a struct-like class!

# Non-supported / Aborted Features

**(Feel free to make a pull request if you really want them to be implemented!)**

## Struct Initializing and Caching

### Group

It is quite useless and complicate to use. In PHPStorm, there isn't an option for overriding properties in the Generate
action.

Therefore, when creating classes as the default values of a Group, people may need to copy and paste the properties
manually.

My opinion, it is better to initialize these kinds of special properties in a constructor rather than writing them in
this weird way.

### Child Structs

Still wasted me quite much time trying to implement.

## Union-types Child Struct

Don't have time to implement...

