<?php

$worktreeRoot = dirname(__DIR__);
$loader = require $worktreeRoot.'/vendor/autoload.php';

// Override PSR-4 namespaces to use the worktree's files.
$loader->setPsr4('App\\', [$worktreeRoot.'/app/']);
$loader->setPsr4('Database\\Factories\\', [$worktreeRoot.'/database/factories/']);
$loader->setPsr4('Database\\Seeders\\', [$worktreeRoot.'/database/seeders/']);
$loader->setPsr4('Tests\\', [$worktreeRoot.'/tests/']);

// The shared classmap takes precedence over PSR-4, so we must override
// classmap entries for App classes that exist in the worktree's app dir.
// addClassMap uses array_merge, so duplicate keys are overwritten by the new value.
$overrides = [];

foreach ($loader->getClassMap() as $class => $path) {
    if (! str_starts_with($class, 'App\\')) {
        continue;
    }

    $localPath = $worktreeRoot.'/app/'.str_replace('\\', '/', substr($class, 4)).'.php';

    if (file_exists($localPath)) {
        $overrides[$class] = $localPath;
    }
}

if (! empty($overrides)) {
    $loader->addClassMap($overrides);
}

return $loader;
