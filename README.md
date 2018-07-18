# magento-catalog-product-update-attributes-multi-store

This is a module which allows an administrator to easily update catalog product attributes for multiple products on multiple stores in the same page.

## Composer Install
1. Add this repository in composer.json
```
composer config repositories.magento-catalog-product-update-attributes-multi-store vcs https://github.com/mimarcel/magento-catalog-product-update-attributes-multi-store
```
2. Install this module in your Composer project
```
composer require mimarcel/magento-catalog-product-update-attributes-multi-store:dev-default
```

## Fresh Composer Install
1. Install a fresh Magento application using instructions from [Magento Vanilla repository](https://github.com/mimarcel/magento-vanilla)
2. Follow instructions from [Composer Install](#composer-install) section

## Manual Install
1. Copy all files from this module to your Magento project, except:
    * .gitignore
    * README.md
    * composer.json
    * modman

## Notes
- This module does add a new option `Update Attributes Multi Store` and disables by default the core functionality for `Update Attributes` using `catalog/update_attributes_multi_store/disable_core_update_attributes` configuration flag.
- This module is not finished. Check todos in the code.
