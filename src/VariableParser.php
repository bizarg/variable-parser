<?php

namespace Bizarg\VariableParser;

use Bizarg\StringHelper\StringHelper;
use Illuminate\Support\Collection;

class VariableParser
{
    protected array $data = [];
    private array $variables = [];
    protected ?string $content = null;
    protected string $signOpen = '';
    protected string $signClose = '';
    protected bool $preview = false;
    private array $search = [];
    private Config $config;
    private mixed $variableData = null;

    public function __construct(?string $content = null, mixed $variableData = null)
    {
        $this->config = new Config();
        $this->setSignOpen($this->config->signOpen());
        $this->setSignClose($this->config->signClose());
        $this->content = $content;
        $this->variableData = $variableData;
    }

    public function parseContent(): ?string
    {
        return $this->variablesFromContent()
            ->prepareVariables()
            ->mergeVariableWithData()
            ->replaceVariables()
            ->content();

    }

    private function variablesFromContent(): self
    {
        preg_match_all("/{$this->getExpression()}/", $this->content(), $matches);

        $this->search = $matches[0];
        $this->variables = $matches[1];

        if (count($this->data)) {
            $this->data = array_map(function ($item) {
                preg_match_all("/{$this->getExpression()}/", $item, $m);

                if (count(array_filter($m))) {
                    $parser = clone $this;
                    $parser->data = array_filter($this->data, function ($i) use ($item) {
                        return $item !== $i;
                    });

                    $parser->setContent($item);
                    $parser->search = array_merge($parser->search, $m[0]);

                    return $parser->parseContent();
                }

                return $item;
            }, $this->data);
        }

        return $this;
    }

    private function prepareVariables(): self
    {
        foreach ($this->variables as $key => $value) {
            $class = $object = null;

            if ($this->config->variableFromClass() && $class = $this->defineVariableClass($value)) {
                $this->variables[$value] = $this->preview() ? $class->preview() : $class->handle();
            }

            if ($this->config->variableFromRelation() && !$class) {
                $collection = collect(explode('.', $value));

                $field = $this->getField($collection->shift());

                if (is_object($field)) {
                    $this->variables[$value] = $this->getProperty($field, $collection);
                }
            }

            if (!isset($this->variables[$value])) {
                $this->variables[$value] = '';
            }

            unset($this->variables[$key]);
        }

        return $this;
    }

    private function getField($field)
    {
        if (is_object($this->variableData) && method_exists($this->variableData, $field)) {
            return $this->variableData->{$field}();
        }

        if (is_array($this->variableData) && isset($this->variableData[$field])) {
            return $this->variableData[$field];
        }
    }


    private function mergeVariableWithData(): self
    {
        $this->variables = array_merge($this->variables, $this->data);

        $this->search = array_unique(array_merge(
            $this->search,
            (array_map(
                function ($item) {
                    return $this->signOpen() . $item . $this->signClose();
                },
                array_keys($this->data))
        )));

        return $this;
    }

    private function replaceVariables(): self
    {
        $this->setContent(strtr($this->content(), array_combine($this->search(), $this->variables())));
        return $this;
    }

    private function getProperty(object $object, Collection $collection): mixed
    {
        $property = $collection->shift();

        if ($collection->count()) {
            return $this->getProperty($object->{$property}, $collection);
        }

        return $object->{$property};
    }

    private function defineVariableClass(string $name): ?VariableInterface
    {
        if (class_exists($className = $this->config->path() . StringHelper::upperCaseCamelCase($name))) {
            return new $className($this->variableData);
        }
        return null;
    }

    public function content(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function signOpen(): string
    {
        return $this->signOpen;
    }

    public function setSignOpen(string $signOpen): self
    {
        $this->signOpen = $signOpen;
        return $this;
    }

    public function signClose(): string
    {
        return $this->signClose;
    }

    public function setSignClose(string $signClose): self
    {
        $this->signClose = $signClose;
        return $this;
    }

    public function search(): array
    {
        return $this->search;
    }

    public function variables(): array
    {
        return $this->variables;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function preview(): bool
    {
        return $this->preview;
    }

    public function setPreview(bool $preview): self
    {
        $this->preview = $preview;
        return $this;
    }

    /**
     * @param object|array|null $variableData
     * @return self
     */
    public function setVariableData($variableData): self
    {
        $this->variableData = $variableData;
        return $this;
    }

    public function getExpression(): string
    {
        return '\\' . join('\\', str_split($this->signOpen()))
            . '([a-zA-z0-9\.]{1,})'
            . '\\' . join('\\', str_split($this->signClose()));
    }
}
