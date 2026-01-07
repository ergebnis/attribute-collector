# attribute-collector

[![Integrate](https://github.com/ergebnis/attribute-collector/workflows/Integrate/badge.svg)](https://github.com/ergebnis/attribute-collector/actions)
[![Merge](https://github.com/ergebnis/attribute-collector/workflows/Merge/badge.svg)](https://github.com/ergebnis/attribute-collector/actions)
[![Release](https://github.com/ergebnis/attribute-collector/workflows/Release/badge.svg)](https://github.com/ergebnis/attribute-collector/actions)
[![Renew](https://github.com/ergebnis/attribute-collector/workflows/Renew/badge.svg)](https://github.com/ergebnis/attribute-collector/actions)

[![Code Coverage](https://codecov.io/gh/ergebnis/attribute-collector/branch/main/graph/badge.svg)](https://codecov.io/gh/ergebnis/attribute-collector)

[![Latest Stable Version](https://poser.pugx.org/ergebnis/attribute-collector/v/stable)](https://packagist.org/packages/ergebnis/attribute-collector)
[![Total Downloads](https://poser.pugx.org/ergebnis/attribute-collector/downloads)](https://packagist.org/packages/ergebnis/attribute-collector)
[![Monthly Downloads](http://poser.pugx.org/ergebnis/attribute-collector/d/monthly)](https://packagist.org/packages/ergebnis/attribute-collector)

This project provides a [`composer`](https://getcomposer.org) package with collectors for collecting [attributes](https://www.php.net/manual/en/language.attributes.overview.php) located on classes, class constants, properties, methods, method parameters, functions, function parameters, and constants.

## Installation

Run

```sh
composer require ergebnis/attribute-collector
```

## Usage

## Attributes

This package provides an `Attribute` that composes the [location](#locations) of an attribute and a concrete instance of the attribute collected at that location.

You can obtain an `Attribute` from an [attribute collection](#attribute-collection) by using a [collector](#collectors) to collect attributes.

## Attribute Collection

This package provides an `AttributeCollection` that composes a collection of [attributes](#attributes).

You can obtain an `AttributeCollection` by using a [collector](#collectors) to collect attributes.

### Filtering an attribute collection by attribute class name

You can filter an attribute collection by attribute class name:

```php
<?php

declare(strict_types=1);

use Ergebnis\AttributeCollector;

$attributeClassName = AttributeCollector\Name\ClassName::fromString(\Deprecated::class);

/** @var AttributeCollector\AttributeCollection $attributeCollector */
$filteredAttributeCollection = $attributeCollector->whereAttributeClassNameEquals($attributeClassName);
```

You can filter an attribute collection by attribute location:

```php
<?php

declare(strict_types=1);

use Ergebnis\AttributeCollector;

$attributeLocation = AttributeCollector\Location\ClassMethodLocation::create(
    AttributeCollector\Name\ClassName::fromString(Foo::class),
    AttributeCollector\Name\MethodName::fromString('bar')
);

/** @var AttributeCollector\AttributeCollection $attributeCollector */
$filteredAttributeCollection = $attributeCollector->whereAttributeLocationEquals($attributeLocation);
```

## Locations

This package provides the following locations that describe where [attributes](#attributes) could be or are located:

- `Location\ClassLocation`
- `Location\ClassConstantLocation`
- `Location\ClassPropertyLocation`
- `Location\ClassMethodLocation`
- `Location\ClassMethodParameterLocation`
- `Location\ConstantLocation`
- `Location\FunctionLocation`
- `Location\FunctionParameterLocation`

## Collectors

This package provides the following collectors for collecting attributes:

- [`Ergebnis\AttributeCollector\Collector\TraversingAttributeFromClassNameCollector`](#collectortraversingattributefromclassnamecollector)
- [`Ergebnis\AttributeCollector\Collector\TraversingAttributeFromFinderCollector`](#collectortraversingattributefromfindercollector)
- [`Ergebnis\AttributeCollector\Collector\TraversingAttributeFromLocationCollector`](#collectortraversingattributefromlocationcollector)

### `Collector\TraversingAttributeFromClassNameCollector`

Use `Collector\TraversingAttributeFromClassNameCollector` to collect [attributes](#attributes) by iterating over and traversing into [locations](#locations) from class names.

#### Collecting attributes by finding class names

In most of the cases, you might want to collect attributes from classes in your project without having to specify these classes explicitly.

For example, you could use [`symfony/finder`](https://github.com/symfony/finder) and [`ergebnis/classy`](https://github.com/ergebnis/classy) to obtain a list of classy constructs names and then collect attributes from these locations:

```php
<?php

declare(strict_types=1);

use Ergebnis\AttributeCollector;
use Ergebnis\Classy;
use Symfony\Component\Finder;

$finder = Finder\Finder::create()
  ->files()
  ->in(__DIR__ . '/src');

$classyConstructCollector = new Classy\Collector\DefaultConstructFromFinderCollector(new Classy\Collector\PhpTokenTokenizeConstructFromSourceCollector()));

$classyConstructs = $classyConstructCollector->collectFromFinder($finder);

$classNames = array_map(static function (Classy\Construct $construct): AttributeCollector\Name\ClassName {
    return AttributeCollector\Name\ClassName::fromString($construct->name()->toString());
}, $classyConstructs);

$attributeCollector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

$attributeCollection = $attributeCollector->collectFromClassName(...$classNames);

foreach ($attributeCollection->toArray() as $attribute) {
    $instance = $attribute->instance();

    // inspect or process concrete attribute instance here
}
```

#### Collecting attributes by specifying class names

In other cases, you might want to collect attributes from specific class names that you already know.

```php
<?php

declare(strict_types=1);

use Ergebnis\AttributeCollector;

$classNames = [
    AttributeCollector\Name\ClassName::fromString(Foo::class),
    AttributeCollector\Name\ClassName::fromString(Bar::class),
    AttributeCollector\Name\ClassName::fromString(Baz::class),
];

$attributeCollector = new AttributeCollector\Collector\TraversingAttributeFromClassNameCollector();

$attributeCollection = $attributeCollector->collectFromClassName(...$classNames);

foreach ($attributeCollection->toArray() as $attribute) {
    $instance = $attribute->instance();

    // inspect or process concrete attribute instance here
}
```
### `Collector\TraversingAttributeFromFinderCollector`

Use `Collector\TraversingAttributeFromFinderCollector` to collect [attributes](#attributes) by iterating over and traversing into [locations](#locations) found in classy constructs from an iterable of `SplFileInfo`s:

```php
<?php

declare(strict_types=1);

use Ergebnis\AttributeCollector;
use Symfony\Component\Finder;

$finder = Finder\Finder::create()
  ->files()
  ->in(__DIR__ . '/src');

$attributeCollector = new AttributeCollector\Collector\TraversingAttributeFromFinderCollector();

$attributeCollection = $attributeCollector->collectFromFinder($finder);

foreach ($attributeCollection->toArray() as $attribute) {
    $instance = $attribute->instance();

    // inspect or process concrete attribute instance here
}
```

> [!NOTE]
>
> This collector is currently limited to collecting attributes by traversing into classy constructs found by [`ergebnis/classy`](https://github.com/ergebnis/classy), such as
>
> - classes
> - enums
> - interfaces
> - traits

### `Collector\TraversingAttributeFromLocationCollector`

Use `Collector\TraversingAttributeFromLocationCollector` to collect [attributes](#attributes) by iterating over and traversing into known [locations](#locations).

```php
<?php

declare(strict_types=1);

use Ergebnis\AttributeCollector;

$locations = [
    AttributeCollector\Location\ClassLocation::create(AttributeCollector\Name\ClassName::fromString(Foo::class)),
    AttributeCollector\Location\ClassPropertyLocation::create(
        AttributeCollector\Name\ClassName::fromString(Foo::class),
        AttributeCollector\Name\PropertyName::fromString('bar')
    ),
    AttributeCollector\Location\ClassMethodLocation::create(
        AttributeCollector\Name\ClassName::fromString(Foo::class),
        AttributeCollector\Name\MethodName::fromString('baz')
    ),
    AttributeCollector\Location\ClassMethodParameterLocation::create(
        AttributeCollector\Name\ClassName::fromString(Foo::class),
        AttributeCollector\Name\MethodName::fromString('baz'),
        AttributeCollector\Name\ParameterName::fromString('qux')
    ),
    AttributeCollector\Location\FunctionLocation::create(AttributeCollector\Name\FunctionName::fromString('baz')),
    AttributeCollector\Location\ConstantLocation::create(AttributeCollector\Name\ConstantName::fromString('BAZ')),
];

$attributeCollector = new AttributeCollector\Collector\TraversingAttributeFromLocationCollector();

$attributeCollection = $attributeCollector->collectFromLocation(...$locations);

foreach ($attributeCollection->toArray() as $attribute) {
    $instance = $attribute->instance();

    // inspect or process concrete attribute instance here
}
```

## Changelog

The maintainers of this project record notable changes to this project in a [changelog](CHANGELOG.md).

## Contributing

The maintainers of this project suggest following the [contribution guide](.github/CONTRIBUTING.md).

## Code of Conduct

The maintainers of this project ask contributors to follow the [code of conduct](https://github.com/ergebnis/.github/blob/main/CODE_OF_CONDUCT.md).

## General Support Policy

The maintainers of this project provide limited support.

## PHP Version Support Policy

This project supports PHP versions with [active and security support](https://www.php.net/supported-versions.php).

The maintainers of this project add support for a PHP version following its initial release and drop support for a PHP version when it has reached the end of security support.

## Security Policy

This project has a [security policy](.github/SECURITY.md).

## License

This project uses the [MIT license](LICENSE.md).

## Social

Follow [@localheinz](https://twitter.com/intent/follow?screen_name=localheinz) and [@ergebnis](https://twitter.com/intent/follow?screen_name=ergebnis) on Twitter.
