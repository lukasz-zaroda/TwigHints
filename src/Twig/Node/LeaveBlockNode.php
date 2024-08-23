<?php

namespace LukaszZaroda\TwigHints\Twig\Node;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\Node;

#[YieldReady]
class LeaveBlockNode extends Node
{
    public function __construct(string $extensionName, string $blockName, string $templateName, string $varName)
    {
        parent::__construct([], [
            'extension_name' => $extensionName,
            'block_name' => $blockName,
            'template_name' => $templateName,
            'var_name' => $varName,
        ]);
    }

    public function compile(Compiler $compiler): void
    {
        $compiler
            ->write(sprintf('$%s = $this->extensions[', $this->getAttribute('var_name')))
            ->repr($this->getAttribute('extension_name'))
            ->raw("];\n")
            ->write(sprintf('yield $%s->leaveBlock(', $this->getAttribute('var_name')))
            ->repr($this->getAttribute('block_name'))
            ->raw(', ')
            ->repr($this->getAttribute('template_name'))
            ->raw(");\n\n")
        ;
    }
}
