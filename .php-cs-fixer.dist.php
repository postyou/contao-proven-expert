<?php

declare(strict_types=1);

$header = <<<'EOF'
    This file is part of postyou/contao-proven-expert.

    (c) POSTYOU Werbeagentur

    @license LGPL-3.0+
    EOF;

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/src',
        __DIR__.'/contao',
    ])
;

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP82Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'echo_tag_syntax' => ['format' => 'short'],
        'no_alternative_syntax' => ['fix_non_monolithic_code' => false],
        'semicolon_after_instruction' => false,
        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;
