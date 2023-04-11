# Smarty component

Includes Smarty template engine for OXID eShop 

## Compatibility

* b-7.0.x branch is compatible with OXID eShop compilation 7.x

## Installation

This component can be installed with:

```bash
composer require oxid-esales/smarty-component
```

## Configuration
Starting from v7, OXID eShop uses Twig as a default templating engine and stores
initial data in Twig template format.
Please run the following command to replace the original initial data with the one compatible with Smarty:

```bash
vendor/bin/doctrine-migrations migrate --configuration vendor/oxid-esales/smarty-component/migration/migrations.yml --db-configuration vendor/oxid-esales/smarty-component/migration/migrations-db.php
```

**_NOTE:_**  Do not run this command if you've already added your custom content.
The data in `oxcontents` DB table will be overwritten!

## License

See LICENSE file for details.

## Bugs and Issues

If you experience any bugs or issues, please report them in the section **OXID eShop (all versions)** under category **Smarty engine** of https://bugs.oxid-esales.com
