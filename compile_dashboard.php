<?php
require __DIR__ . "/vendor/autoload.php";
$fs = new Illuminate\Filesystem\Filesystem();
$compiler = new Illuminate\View\Compilers\BladeCompiler($fs, __DIR__ . "/storage/framework/views");
try {
    $compiled = $compiler->compileString(file_get_contents("resources/views/dashboard.blade.php"));
    echo "Blade compiled OK\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
