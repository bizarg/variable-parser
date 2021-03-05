<?php

namespace Bizarg\VariableParser;

use Illuminate\Support\ServiceProvider;

/**
 * Class VariableParserServiceProvider
 * @package Bizarg\VariableParser
 */
class VariableParserServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        if (method_exists($this, 'publishes')) {
            $this->publishes([
                __DIR__ . '/../config/variable-parser.php' => $this->configPath('variable-parser.php'),
            ], 'variable-parser-config');
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private function configPath($path = ''): string
    {
        return function_exists('config_path')
            ? config_path($path)
            : app()->basePath() . DIRECTORY_SEPARATOR . 'config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

