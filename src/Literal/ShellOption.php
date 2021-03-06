<?php

declare(strict_types=1);

namespace PHPSu\ShellCommandBuilder\Literal;

use PHPSu\ShellCommandBuilder\Exception\ShellBuilderException;
use PHPSu\ShellCommandBuilder\ShellInterface;

/**
 * @internal
 * @psalm-internal PHPSu\ShellCommandBuilder
 */
final class ShellOption extends ShellWord
{
    protected $isOption = true;
    protected $prefix = ShellWord::OPTION_CONTROL;

    /**
     * ShellArgument constructor.
     * @param string $option
     * @param ShellInterface|string $value
     * @throws ShellBuilderException
     */
    public function __construct(string $option, $value = '')
    {
        if (is_string($value) && empty($value)) {
            $this->delimiter = '';
        }
        parent::__construct($option, $value);
    }
}
