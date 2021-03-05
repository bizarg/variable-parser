<?php

namespace Bizarg\VariableParser;

use Bizarg\StringHelper\StringHelper;

/**
 * Class VariableParser
 * @package Bizarg\VariableParser
 */
class VariableParser
{
    /**
     * @var array
     */
    protected array $data = [];
    /**
     * @var array
     */
    protected array $variables = [];
    /**
     * @var string|null
     */
    protected ?string $content = null;
    /**
     * @var string
     */
    protected string $signOpen = '[[';
    /**
     * @var string
     */
    protected string $signClose = ']]';
    /**
     * @var bool
     */
    protected bool $preview = false;
    /**
     * @var array
     */
    protected array $search = [];
    /**
     * @var Config
     */
    private Config $config;

    /**
     * VariableParser constructor.
     */
    public function __construct()
    {
        $this->config = new Config();
        $this->setSignOpen($this->config->signOpen());
        $this->setSignClose($this->config->signClose());
    }

    /**
     * @return $this
     */
    private function prepareVariables(): self
    {
        foreach ($this->variables as $key => $value) {
            if (!$class = $this->defineVariableClass($value)) {
                continue;
            }

            $this->variables[$value] = $this->preview() ? $class->preview() : $class->handle();
            unset($this->variables[$key]);
        }
        return $this;
    }

    /**
     * @return string|null
     */
    public function parseContent(): ?string
    {
        $this->prepareVariables()->mergeVariable()->replaceVariables();
        return $this->content();
    }

    /**
     * @return $this
     */
    private function prepareSearchData(): self
    {
        $this->search = array_map(function ($str) {
            return $this->signOpen . $str . $this->signClose;
        }, array_keys($this->variables));

        return $this;
    }

    /**
     * @param string $name
     * @return VariableInterface|null
     */
    private function defineVariableClass(string $name): ?VariableInterface
    {
        if (class_exists($className = $this->config->path() . StringHelper::upperCaseCamelCase($name))) {
            return new $className($this);
        }
        return null;
    }

    /**
     * @return $this
     */
    private function replaceVariables(): self
    {
        $this->prepareSearchData()
            ->setContent(str_replace($this->search(), array_values($this->variables), $this->content()));
        return $this;
    }

    /**
     * @return $this
     */
    private function mergeVariable(): self
    {
        $this->variables = array_merge($this->variables, $this->data);
        return $this;
    }

    /**
     * @return string
     */
    public function signOpen(): string
    {
        return $this->signOpen;
    }

    /**
     * @param string $signOpen
     * @return self
     */
    public function setSignOpen(string $signOpen): self
    {
        $this->signOpen = $signOpen;
        return $this;
    }

    /**
     * @return string
     */
    public function signClose(): string
    {
        return $this->signClose;
    }

    /**
     * @param string $signClose
     * @return self
     */
    public function setSignClose(string $signClose): self
    {
        $this->signClose = $signClose;
        return $this;
    }

    /**
     * @return array
     */
    public function search(): array
    {
        return $this->search;
    }

    /**
     * @return array
     */
    public function variables(): array
    {
        return $this->variables;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string|null
     */
    public function content(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return bool
     */
    public function preview(): bool
    {
        return $this->preview;
    }

    /**
     * @param bool $preview
     * @return self
     */
    public function setPreview(bool $preview): self
    {
        $this->preview = $preview;
        return $this;
    }
}
