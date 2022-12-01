<?php

declare(strict_types=1);

use PhpCsFixerCustomFixers\Fixer\ConstructorEmptyBracesFixer;
use PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer;
use PhpCsFixerCustomFixers\Fixer\NoCommentedOutCodeFixer;
use PhpCsFixerCustomFixers\Fixer\NoDoctrineMigrationsGeneratedCommentFixer;
use PhpCsFixerCustomFixers\Fixer\NoLeadingSlashInGlobalNamespaceFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessCommentFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessDoctrineRepositoryCommentFixer;
use PhpCsFixerCustomFixers\Fixer\PromotedConstructorPropertyFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceAfterStatementFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceBeforeStatementFixer;
use PhpCsFixerCustomFixers\Fixer\StringableInterfaceFixer;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->registerCustomFixers(new PhpCsFixerCustomFixers\Fixers())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@PHP80Migration' => true,
        '@DoctrineAnnotation' => true,
        'phpdoc_to_comment' => false,
        'single_line_throw' => false,
        'yoda_style' => false,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'array_indentation' => true,
        'method_chaining_indentation' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        ConstructorEmptyBracesFixer::name() => false,
        MultilinePromotedPropertiesFixer::name() => true,
        NoCommentedOutCodeFixer::name() => true,
        NoDoctrineMigrationsGeneratedCommentFixer::name() => true,
        NoLeadingSlashInGlobalNamespaceFixer::name() => true,
        NoUselessCommentFixer::name() => true,
        NoUselessDoctrineRepositoryCommentFixer::name() => true,
        PromotedConstructorPropertyFixer::name() => true,
        SingleSpaceAfterStatementFixer::name() => true,
        SingleSpaceBeforeStatementFixer::name() => true,
        StringableInterfaceFixer::name() => true,
        'global_namespace_import' => ['import_classes' => true, 'import_constants' => false, 'import_functions' => false],
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
