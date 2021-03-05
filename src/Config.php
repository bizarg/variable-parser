<?php

namespace Bizarg\VariableParser;

/**
 * Class Config
 * @package Bizarg\VariableParser
 */
class Config
{
    /**
     * @var array|null
     */
    private ?array $config;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->config = config('variable-parser') ?? include __DIR__ . '/../config/variable-parser.php';
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->trimSlash($this->config['path']);
    }

    /**
     * @return string
     */
    public function signOpen(): string
    {
        return $this->config['signOpen'];
    }

    /**
     * @return string
     */
    public function signClose(): string
    {
        return $this->config['signClose'];
    }

    /**
     * @param string|null $string
     * @return string
     */
    protected function trimSlash(?string $string): ?string
    {
        if (!$string) {
            return null;
        }

        return trim($string, '/') . '\\';
    }
}
