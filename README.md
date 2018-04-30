# Modules Laravel

Package for building modular laravel applications.


## Installation

Require with composer:

```
composer require langleyfoxall/modules-laravel
```

Add service provider to `config/app.php`:

```
LangleyFoxall\Modules\LaravelModuleServiceProvider::class,
```

Publish configuration file:
```
php artisan vendor:publish --provider="LangleyFoxall\Modules\LaravelModuleServiceProvider"
```

## Commands

To create a module simply run:
```
php artisan modules:make [<parent_module>.[<tree_of_modules>.]]<module_name>
```

To delete a module simply run:
```
php artisan modules:delete [<parent_module>.[<tree_of_modules>.]]<module_name>
```

To force generation of configuration file run:
```
php artisan modules:config
```

## Folder Structure

Modules that are generated can be found at `app/Modules`. The configuration file can be found at `config/modules.php`

```
app
    -- Modules
        -- <module_name>
            -- Http
                -- Controllers
                -- Middleware
            -- Migrations
            -- Models
            -- Modules
                -- <sub_module>
                    -- <repeat_structure>
            -- Providers
            -- Routes
            -- Views
```