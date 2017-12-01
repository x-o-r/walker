# Walker

[![Build Status](https://api.travis-ci.org/x-o-r/walker.svg?branch=master)](https://api.travis-ci.org/x-o-r/walker.svg?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/x0r0x/Walker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/x0r0x/Walker/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/x0r0x/Walker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/x0r0x/Walker/?branch=master)

Easily and securely access all targeted data nodes at different depths in data !

## Usage

Retrieve Foo->Bar
```php
(new Walker)
    ->from(
        (object)["Foo" =>
            (object)["Bar" => "Value"]
        ])
    ->with('Foo->Bar')
    ->asString();
  
/* Will return 'Value' */
```
Retrieve Foo->Bar in JSON stream 
```php
     
(new Walker)
    ->fromJson('{
        "Foo": {
            "Bar": "Value"
           }
       }')
    ->with('Foo->Bar')
    ->asString();
  
/* Will return 'Value' */
```
Retrieve Foo->Bar and Walker->Texas->Ranger
```php
     
(new Walker)
    ->from([
        (object)["Foo" =>(object)["Bar" => "Some"]],
        (object)["Walker" =>(object)["Texas" => (object)["Ranger" => "values"]]]
    ])
    ->with('Foo->Bar')
    ->with('Walker->Texas->Ranger')
    ->asString();
  
/* Will return 'Some, values' */
```
With values located at different depths
```php
           
(new Walker)
    ->from([
        (object)["Walker" =>
            (object)["Texas" =>
                (object) ['Ranger' => 'All']
            ]
        ],
        (object)["Walker" => [
                (object)["Texas" => (object)["Ranger" => "targets"]],
                (object)['Texas' => (object)['Ranger' => 'are']],
            ]
        ],
        (object)['Walker' => (object)['Texas' => (object)['Ranger' => 'retrieved']]]
    ])
    ->with('Walker->Texas->Ranger')
    ->asString(function($founds) {
        return join(' ', $founds);
    });
  
/* Will return 'All targets are retrieved' */
```
