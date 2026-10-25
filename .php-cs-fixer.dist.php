<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())->in(__DIR__); 

$rules = [
    '@auto' => true,
    '@auto:risky' => true,
    '@PhpCsFixer:risky' => true,
    'final_internal_class' => false,
    'php_unit_data_provider_return_type' => false,
    'php_unit_data_provider_name' => false,
    'php_unit_test_case_static_method_calls' => [
        'call_type' => 'this',
    ],
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'no_extra_blank_lines' => [
        'tokens' => [
            'attribute',
            'break',
            'case',
            'comma',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'switch',
            'throw',
        ],
    ],
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules($rules);
