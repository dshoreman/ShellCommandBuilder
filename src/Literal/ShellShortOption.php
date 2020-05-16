<?php

declare(strict_types=1);

namespace PHPSu\ShellCommandBuilder\Literal;

use PHPSu\ShellCommandBuilder\ShellInterface;

final class ShellShortOption extends ShellWord
{
    protected $isShortOption = true;
    protected $prefix = ShellWord::SHORT_OPTION_CONTROL;

    /**
     * ShellArgument constructor.
     * @param string $option
     * @param ShellInterface|string $value
     */
    public function __construct(string $option, $value)
    {
        if (is_string($value) && empty($value)) {
            $this->delimiter = '';
        }
        parent::__construct($option, $value);
    }
}
