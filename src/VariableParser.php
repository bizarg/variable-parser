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
    private array $variables = [];
    /**
     * @var string|null
     */
    protected ?string $content = null;
    /**
     * @var string
     */
    protected string $signOpen = '';
    /**
     * @var string
     */
    protected string $signClose = '';
    /**
     * @var bool
     */
    protected bool $preview = false;
    /**
     * @var array
     */
    private array $search = [];
    /**
     * @var Config
     */
    private Config $config;
    /**
     * @var mixed
     */
    private $variableData;

    /**
     * VariableParser constructor.
     * @param string $content
     * @param mixed $variableData
     */
    public function __construct(string $content, $variableData)
    {
        $this->config = new Config();
        $this->setSignOpen($this->config->signOpen());
        $this->setSignClose($this->config->signClose());
        $this->content = $content;
        $this->variableData = $variableData;
    }

    /**
     * @return self
     */
    private function prepareVariables(): self
    {
        foreach ($this->variables as $key => $value) {
            if ($class = $this->defineVariableClass($value)) {
                $this->variables[$value] = $this->preview() ? $class->preview() : $class->handle();
            }

            if (!$class) {
                $this->variables[$value] = $this->data()[$value] ?? null;
            }

            unset($this->variables[$key]);
        }
        return $this;
    }

    /**
     * @return self
     */
    private function variablesFromContent(): self
    {
        $regExp = '\\' . join('\\', str_split($this->signOpen()))
            . '([a-zA-z\.]{1,})'
            . '\\' . join('\\', str_split($this->signClose()));

        preg_match_all("/$regExp/", $this->content(), $matches);

        $this->search = $matches[0];
        $this->variables = $matches[1];

        return $this;
    }

    /**
     * @return string|null
     */
    public function parseContent(): ?string
    {
        return $this->variablesFromContent()
            ->prepareVariables()
            ->mergeVariableWithData()
            ->replaceVariables()
            ->content();
    }

    /**
     * @param string $name
     * @return VariableInterface|null
     */
    private function defineVariableClass(string $name): ?VariableInterface
    {
        if (class_exists($className = $this->config->path() . StringHelper::upperCaseCamelCase($name))) {
            return new $className($this->variableData);
        }
        return null;
    }

    /**
     * @return self
     */
    private function replaceVariables(): self
    {
        $this->setContent(str_replace($this->search, array_values($this->variables), $this->content()));
        return $this;
    }

    /**
     * @return self
     */
    private function mergeVariableWithData(): self
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
