# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

For a full diff see [`e66cfff...main`][e66cfff...main].

### Added

- Added `Attribute` ([#1]), by [@localheinz]
- Added `Location\ClassLocation` ([#2]), by [@localheinz]
- Added `Location\ClassConstantLocation` ([#3]), by [@localheinz]
- Added `Location\ClassPropertyLocation` ([#4]), by [@localheinz]
- Added `Location\ClassMethodLocation` ([#5]), by [@localheinz]
- Added `Location\ClassMethodParameterLocation` ([#6]), by [@localheinz]
- Added `Location\FunctionLocation` ([#7]), by [@localheinz]
- Added `Location\FunctionParameterLocation` ([#8]), by [@localheinz]
- Added `Location\ConstantLocation` ([#9]), by [@localheinz]
- Added `AttributeCollection` ([#10]), by [@localheinz]
- Added `Collector\TraversingAttributeFromLocationCollector`, which allows collecting attributes by iterating over and traversing into known locations ([#11]), by [@localheinz]
- Added `Collector\TraversingAttributeFromClassNameCollector`, which allows collecting attributes by iterating over and traversing into locations from class names ([#12]), by [@localheinz]
- Added `Name\ClassName::equals()`, `Name\ConstantName::equals()`, `Name\FunctionName::equals()`, `Name\MethodName::equals()`, `Name\ParameterName::equals()`, and `Name\PropertyName::equals()` ([#13]), by [@localheinz]

[e66cfff...main]: https://github.com/ergebnis/attribute-collector/compare/e66cfff...main

[#1]: https://github.com/ergebnis/attribute-collector/pull/1
[#2]: https://github.com/ergebnis/attribute-collector/pull/2
[#3]: https://github.com/ergebnis/attribute-collector/pull/3
[#4]: https://github.com/ergebnis/attribute-collector/pull/4
[#5]: https://github.com/ergebnis/attribute-collector/pull/5
[#6]: https://github.com/ergebnis/attribute-collector/pull/6
[#7]: https://github.com/ergebnis/attribute-collector/pull/7
[#8]: https://github.com/ergebnis/attribute-collector/pull/8
[#9]: https://github.com/ergebnis/attribute-collector/pull/9
[#10]: https://github.com/ergebnis/attribute-collector/pull/10
[#11]: https://github.com/ergebnis/attribute-collector/pull/11
[#12]: https://github.com/ergebnis/attribute-collector/pull/12
[#13]: https://github.com/ergebnis/attribute-collector/pull/13

[@localheinz]: https://github.com/localheinz
