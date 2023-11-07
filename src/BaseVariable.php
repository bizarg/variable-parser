<?php

namespace Bizarg\VariableParser;

class BaseVariable implements VariableInterface
{
    public function __construct(protected $data)
    {
    }

    public function preview(): ?string
    {
        return 'preview';
    }

    public function handle(): ?string
    {
        return 'handle';
    }
}
