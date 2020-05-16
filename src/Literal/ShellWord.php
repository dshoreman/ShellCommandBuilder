<?php

declare(strict_types=1);

namespace PHPSu\ShellCommandBuilder\Literal;

use PHPSu\ShellCommandBuilder\Exception\ShellBuilderException;
use PHPSu\ShellCommandBuilder\ShellInterface;

/**
 * Representing the basic element of a Shell Command, a single literal aka "word"
 * @internal
 * @psalm-internal PHPSu\ShellCommandBuilder\Literal
 * @package PHPSu\ShellCommandBuilder\Literal
 */
class ShellWord implements ShellInterface
{
    protected const OPTION_CONTROL = '--';
    protected const SHORT_OPTION_CONTROL = '-';
    protected const EQUAL_CONTROL = '=';

    /** @var bool */
    protected $isShortOption = false;
    /** @var bool */
    protected $isOption = false;
    /** @var bool */
    protected $isArgument = false;
    /** @var bool */
    protected $isEnvironmentVariable = false;
    /** @var bool */
    protected $isEscaped = true;
    /** @var bool */
    protected $spaceAfterValue = true;
    /** @var bool */
    protected $useAssignOperator = false;
    /** @var bool */
    protected $nameUpperCase = false;
    /** @var string */
    protected $prefix = '';
    /** @var string */
    protected $suffix = ' ';
    /** @var string */
    protected $delimiter = ' ';
    /** @var string */
    protected $argument;
    /** @var string|ShellInterface */
    protected $value;

    /**
     * The constructor is protected, you must choose one of the children
     * @param string $argument
     * @param string|ShellInterface $value
     */
    protected function __construct(string $argument, $value = '')
    {
        $this->argument = $argument;
        $this->value = $value;
    }

    public function setEscape(bool $isEscaped): self
    {
        $this->isEscaped = $isEscaped;
        return $this;
    }

    public function setSpaceAfterValue(bool $spaceAfterValue): self
    {
        $this->spaceAfterValue = $spaceAfterValue;
        return $this;
    }

    public function setAssignOperator(bool $useAssignOperator): self
    {
        $this->useAssignOperator = $useAssignOperator;
        return $this;
    }

    public function setNameUppercase(bool $uppercaseName): self
    {
        $this->nameUpperCase = $uppercaseName;
        return $this;
    }

    protected function validate(): void
    {
        /** @psalm-suppress DocblockTypeContradiction */
        if (!(is_string($this->value) || $this->value instanceof ShellInterface)) {
            throw new ShellBuilderException('Value must be an instance of ShellInterface or a string');
        }
    }

    private function prepare(): void
    {
        $this->validate();
        if (!$this->spaceAfterValue) {
            $this->suffix = '';
        }
        if ($this->useAssignOperator) {
            $this->delimiter = self::EQUAL_CONTROL;
        }
        if (!empty($this->argument) && $this->nameUpperCase) {
            $this->argument = strtoupper($this->argument);
        }
    }

    /**
     * @param bool $debug
     * @return array<mixed>|string
     */
    private function getValue(bool $debug = false)
    {
        $word = $this->value;
        if ($word instanceof ShellInterface) {
            if ($debug) {
                return $word->__toArray();
            }
            $word = (string)$word;
        }
        if ($this->isEscaped && !empty($word)) {
            $word = escapeshellarg($word);
        }
        return $word;
    }

    /**
     * @return array<string, mixed>
     */
    public function __toArray(): array
    {
        $this->prepare();
        return [
            'isArgument' => $this->isArgument,
            'isShortOption' => $this->isShortOption,
            'isOption' => $this->isOption,
            'isEnvironmentVariable' => $this->isEnvironmentVariable,
            'escaped' => $this->isEscaped,
            'withAssign' => $this->useAssignOperator,
            'spaceAfterValue' => $this->spaceAfterValue,
            'value' => $this->getValue(true),
            'argument' => $this->argument,
        ];
    }

    public function __toString(): string
    {
        $this->prepare();
        /** @var string $value */
        $value = $this->getValue();
        return sprintf(
            '%s%s%s%s%s',
            $this->prefix,
            $this->argument,
            $this->delimiter,
            $value,
            $this->suffix
        );
    }
}
