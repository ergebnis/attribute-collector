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

use Ergebnis\License;
use Ergebnis\PhpCsFixer;
use PhpCsFixer\Finder;

$license = License\Type\MIT::markdown(
    __DIR__ . '/LICENSE.md',
    License\Range::since(
        License\Year::fromString('2025'),
        new DateTimeZone('UTC'),
    ),
    License\Holder::fromString('Andreas Möller'),
    License\Url::fromString('https://github.com/ergebnis/attribute-collector'),
);

$license->save();

$ruleSet = PhpCsFixer\Config\RuleSet\Php80::create()
    ->withHeader($license->header())
    ->withRules(PhpCsFixer\Config\Rules::fromArray([
        'php_unit_test_case_static_method_calls' => [
            'methods' => [
                'createStub' => 'this',
            ],
        ],
    ]));

$finder = Finder::create()
    ->exclude([
        '.build/',
        '.github/',
        '.note/',
    ])
    ->ignoreDotFiles(false)
    ->in(__DIR__)
    ->notPath([
        'test/Fixture/constants.php',
    ]);

$config = PhpCsFixer\Config\Factory::fromRuleSet($ruleSet);

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php-cs-fixer.cache');
$config->setFinder($finder);

return $config;
