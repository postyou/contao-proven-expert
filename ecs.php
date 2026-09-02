<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Fixer\FunctionNotation\MultilinePromotedPropertiesFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

$header = <<<'EOF'
    This file is part of postyou/contao-proven-expert.

    (c) POSTYOU Werbeagentur

    @license LGPL-3.0+
    EOF;

return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withConfiguredRule(HeaderCommentFixer::class, ['header' => $header])
    ->withConfiguredRule(MultilinePromotedPropertiesFixer::class, ['keep_blank_lines' => true])
    ->withRules([SingleLineEmptyBodyFixer::class])
    ->withSkip([
        MethodChainingIndentationFixer::class => [
            'config/*.php',
            '*/ContaoProvenExpertBundle.php',
        ],
    ])
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/contao',
        __DIR__.'/src',
    ])
    ->withSpacing(Option::INDENTATION_SPACES, "\n")
    ->withParallel()
    ->withCache(sys_get_temp_dir().'/ecs_default_cache')
;
