<?php
$finder = PhpCsFixer\Finder::create()
    ->exclude(
        [
            'vendor',
        ]
    )
    ->notPath('_ide_helper.php')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@PSR2' => true,
            'array_syntax' => ['syntax' => 'short'],
            'is_null' => true,
            'mb_str_functions' => true,
            'modernize_types_casting' => true,
            'hash_to_slash_comment' => true,
            'cast_spaces' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'new_with_braces' => true,
            'no_empty_statement' => true,
            'no_whitespace_before_comma_in_array' => true,
            'no_whitespace_in_blank_line' => true,
            'no_useless_return' => true,
            'ternary_operator_spaces' => true,
            'trailing_comma_in_multiline_array' => true,
            'space_after_semicolon' => true,
            'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
            'phpdoc_no_empty_return' => true,
            'phpdoc_order' => true,
            'phpdoc_types' => true,
            'phpdoc_var_without_name' => true,
            'no_trailing_comma_in_singleline_array' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_extra_consecutive_blank_lines' => true,
            'concat_space' => ['spacing' => 'one'],
            'blank_line_before_return' => true,
        ]
    )
    ->setFinder($finder);
