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


$baseNamespaceEnv = getenv('APP_NAMESPACE');

if (!empty($baseNamespaceEnv)) {
    $baseNamespace = studlyCaps($baseNamespaceEnv);
    echo "Changing namespace 'App' to '{$baseNamespace}' in files:\n";

    // rename base namespace in app class
    replaceIfFileExists('src/Application.php', function ($content) use ($baseNamespace) {
        return preg_replace('/^namespace\s+App;$/m', "namespace {$baseNamespace};", $content);
    });

    // rename base namespace in index
    replaceIfFileExists('public/index.php', function ($content) use ($baseNamespace) {
        return str_replace('\\App\\Application', "\\{$baseNamespace}\\Application", $content);
    });
}

// update composer.json (cleanup + optional namespace)
replaceIfFileExists('composer.json', function ($content) use ($baseNamespaceEnv) {
    $composerJson = json_decode($content, true);

    if (!empty($baseNamespaceEnv)) {
        $baseNamespace = studlyCaps($baseNamespaceEnv);
        unset($composerJson['autoload']['psr-4']['App\\']);
        $composerJson['autoload']['psr-4']["{$baseNamespace}\\"] = "src/";
    }

    // cleaning composer.json so that developers start with a basic composer.json
    unset($composerJson['scripts']);
    unset($composerJson['name']);
    unset($composerJson['description']);
    $composerJson['type'] = 'app';
    unset($composerJson['license']);

    return json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
});

// clean up .gitignore for the final project
replaceIfFileExists('.gitignore', function ($content) {
    // remove submodule exception and restore standard vendor ignore
    $content = preg_replace('/!\/vendor\/yosko\/watamelo\/?\n?/', '', $content);
    $content = str_replace('/vendor/*', '/vendor/', $content);
    return $content;
});

// remove this setup script
$setup = basename(__FILE__);
unlink($setup);
echo " ✔ {$setup} deleted\n";

echo "\n Setup finalized!\n";
// next: composer dump-autoload
