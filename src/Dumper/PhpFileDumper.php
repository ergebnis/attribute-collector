<?php

declare(strict_types=1);

/**
 * Copyright (c) 2025-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/attribute-collector
 */

namespace Ergebnis\AttributeCollector\Dumper;

use Ergebnis\AttributeCollector\AttributeCollection;
use Ergebnis\AttributeCollector\Exception;
use Ergebnis\AttributeCollector\Location;

final class PhpFileDumper
{
    public function dump(AttributeCollection $attributeCollection): string
    {
        $attributes = $attributeCollection->toArray();

        $lines = [];

        $lines[] = '<?php';
        $lines[] = '';
        $lines[] = 'declare(strict_types=1);';
        $lines[] = '';

        if ([] === $attributes) {
            $lines[] = 'return \Ergebnis\AttributeCollector\AttributeCollection::create();';
            $lines[] = '';

            return \implode("\n", $lines);
        }

        $lines[] = 'return \Ergebnis\AttributeCollector\AttributeCollection::create(';

        foreach ($attributes as $index => $attribute) {
            $locationCode = self::exportLocation($attribute->location());
            $instanceCode = $this->exportInstance($attribute->instance());

            $lines[] = '    \Ergebnis\AttributeCollector\Attribute::create(';
            $lines[] = '        ' . $locationCode . ',';
            $lines[] = '        ' . $instanceCode . ',';
            $lines[] = '    ),';
        }

        $lines[] = ');';
        $lines[] = '';

        return \implode("\n", $lines);
    }

    private static function exportLocation(Location\Location $location): string
    {
        if ($location instanceof Location\ClassLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\ClassLocation::create(\Ergebnis\AttributeCollector\Name\ClassName::fromString(%s))',
                \var_export($location->className()->toString(), true),
            );
        }

        if ($location instanceof Location\ClassConstantLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\ClassConstantLocation::create(\Ergebnis\AttributeCollector\Name\ClassName::fromString(%s), \Ergebnis\AttributeCollector\Name\ConstantName::fromString(%s))',
                \var_export($location->className()->toString(), true),
                \var_export($location->constantName()->toString(), true),
            );
        }

        if ($location instanceof Location\ClassPropertyLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\ClassPropertyLocation::create(\Ergebnis\AttributeCollector\Name\ClassName::fromString(%s), \Ergebnis\AttributeCollector\Name\PropertyName::fromString(%s))',
                \var_export($location->className()->toString(), true),
                \var_export($location->propertyName()->toString(), true),
            );
        }

        if ($location instanceof Location\ClassMethodLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\ClassMethodLocation::create(\Ergebnis\AttributeCollector\Name\ClassName::fromString(%s), \Ergebnis\AttributeCollector\Name\MethodName::fromString(%s))',
                \var_export($location->className()->toString(), true),
                \var_export($location->methodName()->toString(), true),
            );
        }

        if ($location instanceof Location\ClassMethodParameterLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\ClassMethodParameterLocation::create(\Ergebnis\AttributeCollector\Name\ClassName::fromString(%s), \Ergebnis\AttributeCollector\Name\MethodName::fromString(%s), \Ergebnis\AttributeCollector\Name\ParameterName::fromString(%s))',
                \var_export($location->className()->toString(), true),
                \var_export($location->methodName()->toString(), true),
                \var_export($location->parameterName()->toString(), true),
            );
        }

        if ($location instanceof Location\ConstantLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\ConstantLocation::create(\Ergebnis\AttributeCollector\Name\ConstantName::fromString(%s))',
                \var_export($location->constantName()->toString(), true),
            );
        }

        if ($location instanceof Location\FunctionLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\FunctionLocation::create(\Ergebnis\AttributeCollector\Name\FunctionName::fromString(%s))',
                \var_export($location->functionName()->toString(), true),
            );
        }

        if ($location instanceof Location\FunctionParameterLocation) {
            return \sprintf(
                '\Ergebnis\AttributeCollector\Location\FunctionParameterLocation::create(\Ergebnis\AttributeCollector\Name\FunctionName::fromString(%s), \Ergebnis\AttributeCollector\Name\ParameterName::fromString(%s))',
                \var_export($location->functionName()->toString(), true),
                \var_export($location->parameterName()->toString(), true),
            );
        }

        throw new \RuntimeException(\sprintf(
            'Unsupported location type "%s".',
            $location::class,
        ));
    }

    /**
     * @throws Exception\AttributeInstanceCouldNotBeExported
     */
    private function exportInstance(object $instance): string
    {
        $reflection = new \ReflectionClass($instance);
        $constructor = $reflection->getConstructor();

        $className = '\\' . $instance::class;

        if (null === $constructor || [] === $constructor->getParameters()) {
            return \sprintf('new %s()', $className);
        }

        $arguments = [];

        foreach ($constructor->getParameters() as $parameter) {
            $property = $reflection->getProperty($parameter->getName());
            $property->setAccessible(true);
            $value = $property->getValue($instance);

            $arguments[] = $this->exportValue($value, $instance, $parameter->getName());
        }

        return \sprintf(
            'new %s(%s)',
            $className,
            \implode(', ', $arguments),
        );
    }

    /**
     * @param mixed $value
     *
     * @throws Exception\AttributeInstanceCouldNotBeExported
     */
    private function exportValue($value, object $instance, string $parameterName): string
    {
        if (null === $value) {
            return 'null';
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (\is_int($value)) {
            return (string) $value;
        }

        if (\is_float($value)) {
            return \var_export($value, true);
        }

        if (\is_string($value)) {
            return \var_export($value, true);
        }

        if (\is_array($value)) {
            return $this->exportArray($value, $instance, $parameterName);
        }

        if ($value instanceof \UnitEnum) {
            return '\\' . $value::class . '::' . $value->name;
        }

        throw Exception\AttributeInstanceCouldNotBeExported::forAttributeInstanceWithUnsupportedParameterValueType(
            $instance,
            $parameterName,
            \get_debug_type($value),
        );
    }

    /**
     * @param array<int|string, mixed> $value
     *
     * @throws Exception\AttributeInstanceCouldNotBeExported
     */
    private function exportArray(array $value, object $instance, string $parameterName): string
    {
        if ([] === $value) {
            return '[]';
        }

        $isList = \array_values($value) === $value;

        $parts = [];

        foreach ($value as $key => $item) {
            $exportedValue = $this->exportValue($item, $instance, $parameterName);

            if ($isList) {
                $parts[] = $exportedValue;
            } else {
                $exportedKey = \is_int($key) ? (string) $key : \var_export($key, true);
                $parts[] = $exportedKey . ' => ' . $exportedValue;
            }
        }

        return '[' . \implode(', ', $parts) . ']';
    }
}
