<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

$header = <<<'EOF'
This file is part of postyou/contao-proven-expert.

(c) POSTYOU Werbeagentur

@license LGPL-3.0+
EOF;

return ECSConfig::configure()
    ->withPhpCsFixerSets(
        php81Migration: true,
        php80MigrationRisky: true,
        phpCsFixer: true,
        phpCsFixerRisky: true,
    )
    ->withSkip([
        MethodChainingIndentationFixer::class => [
           '*/ContaoProvenExpertBundle.php'
        ],
        MethodArgumentSpaceFixer::class,
    ])
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/contao',
    ])
    ->withConfiguredRule(HeaderCommentFixer::class, ['header' => $header])
    ->withSpacing(Option::INDENTATION_SPACES, "\n")
    ->withParallel()
    ->withCache(sys_get_temp_dir().'/ecs_default_cache')
;
