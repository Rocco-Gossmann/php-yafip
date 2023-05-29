# YaFiP - (Y)et (a)nother (F)ramework (i)n (P\)HP

## What is this?

This is an attempt at creating a PHP based Framework for handling Websites in a more modern fashion. 
All the "Cool kids" use Node nowadays, so why not try something new for a change.

### The Goal ...
... is to create something that lets the Programmer write PHP code in a similar way to lets say Svelte-Components or Smarty-Templates,
but then also compile that code into a optimized, complete webpage that can be served quickly to clients.

Including the ability to update only parts of the served page on the client. (<-- this part is not yet implemented though)


## How it works / Getting started:

### Setup 

#### The Environment:
Your Server needs to define 2 Environment Variables:
This can be done in your Apache vhost config, via Apaches .htaccess or your any other means of your system.

These pathes need to be **relative** to your Servers `$_SERVER['DOCUMENT_ROOT']`

- `ROGOSS_YAFIP_PAGESROOT = 'src/pages'`         
- `ROGOSS_YAFIP_COMPONENTSROOT = 'src/components'`



#### Submodules 
This project ***requires*** 2 Submodule to be loaded. 
- `rogoss\core`  [Github: rocco-gossmann/php-core](https://github.com/Rocco-Gossmann/php-core)
- `rogoss\php-yafip-lib` [Github: rocco-gossmann/php-yafip-lib](https://github.com/Rocco-Gossmann/php-yafip-lib)

Initialize them via
```bash
git submodule init && git submodule update
```

The project also contain a 3rd Submodule, that proviedes a way set up a quick autoloader.
- `rogoss\workspace` [Github: rocco-gossmann/php-workspace](https://github.com/Rocco-Gossmann/php-workspace)


Feel free to replace it, with what ever autoloading method you prefere.
Just make sure, the two submodules above are covered by your new method.



### Creating your first Page.

In your `pages` folder (the you you set up with the `ROGOSS_YAFIP_PAGESROOT` Environment var)
create a new subfolder called `index`. Now you should have `src/pages/index` in your project.

#### the HTML side of things
Next in this folder (`src/pages/index`) create a new file called `layout.html`.

You should now have `src/pages/index/layout.html`. Open it in your Editor of choice and paste the following into it:

```html
<!DOCTYPE html>
<html>
    <body>
        <h1> Hello World !!! </h1>
        <p>
            it has been [[--time--]] seconds since the 01. Jan. 1970.
        </p>
    </body>
</html>
```
Notice the `[[--time--]]`  part. This is a Placeholder and can either lead to a component or data to be rendered
For the sake of this intro, this one is used as data.


#### the Data side of things
next to your `src/pages/index/layout.html` create a new file `data.php`. This one will be responsible for, you guessed it, 
filling in the data fields defined in the `layout.html`

So open your newly created `src/pages/index/data.php` and paste the following.
```php
<?php 
    return [
        'time' => time()
    ]
```
Noticed the `return`?  PHPs include can return a value, ` $var = include "filename.php" `

YaFiP makes use of that behavior to avoid having to introduce new variables into the scope and keeping `data.php` isolated.

At the same time, what is defined in `data.php` will stay in `data.php`. 
The only way of getting access to global ressources being the Super-Globals and using `global $varname`.


#### The Route

while this framework does not privide routing. It should integrat well with existing routing methods.

Lets say we use Apache.

create an `index.php` in your servers DOCUMENT_ROOT and paste the following into it:
```php
<?php require_once __DIR__ . "/loader.php";  //<-- if you use the loading methods 
                                             // of this project this is the autoloader

use rogoss\yafip\Page;

Page::load('index')->render();
```

on `Page::load(` all we need to provide is the name of the subfolder containing our layout.
The rest will be handled by the environment var we set up earlier.


## Roadmap:
- ⬜ Figure out, how to do Wikis on GitHub
- ⬜ Explain Components
- ✔️ Finish example
- ✔️ set everything up to be cloned



## Misc:

### Motivation:

I love PHP. You can just install it and be done. Each script works on every system, that supports PHP.

No depencys no, Package managers, nothing.  That does not mean, there are no packages.
Tools like Smarty and Laravel for example are excelent, but also come with some 3rd party dependecys and some function overhead.

So I trie to create my own kind of Framework / Engine for handling Websites. Will it be as capable as any of the two mentioned above.
Of course not, but it will be an interessting experiement.
