<?php

namespace Bizarg\VariableParser;

/**
 * Class VariableInterface
 * @package Bizarg\VariableParser
 */
interface VariableInterface
{
    /**
     * @return string|null
     */
    public function preview(): ?string;

    /**
     * @return string|null
     */
    public function handle(): ?string;
}
