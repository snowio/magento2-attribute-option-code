# Magento 2 Attribute Option Code
## Description
Magento 2 module that that adds a unique code to an attribute option. Attribute options can now be created by remote clients 
with an identifier (attribute option code) that the client can specify and later reference during product saves. Attribute options 
that were created with the client can also be deleted by specifing the attribute option code that belongs to the attribute option.
Please refer to the endpoint table for uri endpoint paths.

## Prerequisites
* PHP 5.6 or newer
* Composer  (https://getcomposer.org/download/).
* `magento/framework` 100 or newer
* `magento/module-eav` 100 or newer
* `magento/module-catalog` 101 or newer

## Installation
```
composer require snowio/magento2-attribute-option-code
php bin/magento module:enable SnowIO_AttributeOptionCode
php bin/magento setup:upgrade
```

## Usage
### Endpoint table
| HTTP Method | URI Path                                                                                                   | Description                                                                                                                                                  |
|-------------|------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| POST        | `/V1/coded-attribute-options`                                                                              | Creates an Attribute option with a corresponding option code. See *Creating a coded attribute option*                                                        |
| DELETE      | `/V1/coded-attribute-options/entity-type/:entityType/attribute-code/:attributeCode/option-code/:optionCode`| Deletes an attribute option code. See  *Deleting attribute options*  for more information.                                                                   |          
| PUT         | `/V1/products-with-option-codes/:sku`                                                                      | Saves a product with attribute option codes specified instead of attribute option IDs'. See *Specifying attribute options in products* for more information  |


### Creating a coded attribute option
#### Request message body (JSON)
```json
{
    "entity_type" : 4,
    "attribute_code" : "testAttribute",
    "option" : {
        "label": "foo",
        "value": "bar",
        "sort_order": 0,
        "is_default": true,
        "store_labels": [
            {
                "store_code" : "testStore",
                "label": "Foo Label Test"
            }
        ]
    }
}
```

##### Message body description
* `entity_type` *integer* : The entity type that the attribute option corresponds to.
* `attribute_code` *string* : The attribute code of the that corresponds to the option
* `option` :
    * `label` *string* : The option label
    * `value` *string* : The option code
    * `sort_order` *integer* : The option's sort order 
    * `is_default` *boolean* : default option flag
    * `store_labels` *1..n* :
        * `store_code` *string* : The store code for the option
        * `label` *string* : The option label

### Deleting attribute options
* `:entityType` *integer* : The entity type that the attribute option corresponds to.
* `:attributeCode` *string* : The attribute code that the attribute option corresponds to.
* `:optionCode` *string* : The attribute option code.

### Specifying attribute options in products
#### URI parameters
* `:sku` *string* : The products sku.

#### Request message body (JSON)
```json
{
    "product": {
        "type_id": "simple",
        "sku": "test-from-snowio-simple",
        "attribute_set_id": 4,
        "name": "test from snow.io simple",
        "price": 10,
        "visibility": 4,
        "status": 1,
        "custom_attributes": {
            "testAttribute": "bar"
        }
    }
}
```

**NOTE** that the **value** of the custom attribute `testAttribute` is the **attribute option code** `bar`.

## License
This software is licensed under the MIT License. [View the license](LICENSE)
