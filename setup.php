<?php

declare(strict_types=1);

// turn kebab-case or snake_case into StudlyCaps
function studlyCaps(string $name): string
{
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
}

function replaceIfFileExists(string $path, callable $replaceFunction): void
{
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $content = $replaceFunction($content);
        file_put_contents($path, $content);
        echo " ✔ {$path} updated\n";
    }
}


$projectName = $argv[1] ?? basename(getcwd());
$baseNamespace = studlyCaps($projectName);
echo "Changing namespace 'App' to '{$baseNamespace}' in files:\n";

// rename base namespace in app class
replaceIfFileExists(__DIR__ . '/src/Application.php', function ($content) use ($baseNamespace) {
    return preg_replace('/^namespace\s+App;$/m', "namespace {$baseNamespace};", $content);
});

// rename base namespace in index
replaceIfFileExists(__DIR__ . '/public/index.php', function ($content) use ($baseNamespace) {
    return str_replace('\\App\\Application', "\\{$baseNamespace}\\Application", $content);
});

// update base namespace for PSR-4 autoloading in composer.json
replaceIfFileExists(__DIR__ . '/composer.json', function ($content) use ($baseNamespace) {
    $composerJson = json_decode($content, true);
    unset($composerJson['autoload']['psr-4']['App\\']);
    $composerJson['autoload']['psr-4']["{$baseNamespace}\\"] = "src/";

    // remove project creation script so that developpers start with a basic composer.json
    unset($composerJson['scripts']['post-update-cmd']);

    return json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// remove composer.lock in .gitignore
replaceIfFileExists(__DIR__ . '/src/Application.php', function ($content) use ($baseNamespace) {
    return preg_replace('/^namespace\s+App;$/m', "namespace {$baseNamespace};", $content);
});

// remove this setup script
unlink(__FILE__);

echo "\n Setup finalized!\n";
// next: composer dump-autoload
