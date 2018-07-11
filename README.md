# Lamoda PHP Enum bundle
Utility wrapper for https://github.com/paillechat/php-enum

## Installation

Usage is as simple as 

```bash
composer require lamoda/enum-bundle:^1.0
```

```php
// Kernel

public function registerBundles()
{
    // ...
    $bundles[] = new \Lamoda\EnumBundle\LamodaEnumBundle();
    // ...
}
```

```yaml
# config.yml
lamoda_enum:
    dbal_types:
        # short example
        my_domain_enum: My\Domain\Enum
        # full example
        my_domain_enum_2: 
          class: My\Domain\Enum
          # identical strategy saves enum as its name as is, no conversion
          # lowercase strategy converts enum name to lowercase and vice versa on fetch
          strategy: identical    
```

```php
class MyEntity
{
    /** @ORM\Column(type="my_domain_enum") */
    private $value;
}
```

This will enable conversion of value field from your enum
