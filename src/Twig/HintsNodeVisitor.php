<?php

namespace LukaszZaroda\TwigHints\Twig;

use LukaszZaroda\TwigHints\Twig\Node\EnterBlockNode;
use LukaszZaroda\TwigHints\Twig\Node\EnterModuleNode;
use LukaszZaroda\TwigHints\Twig\Node\LeaveBlockNode;
use LukaszZaroda\TwigHints\Twig\Node\LeaveModuleNode;
use Twig\Environment;
use Twig\Node\BlockNode;
use Twig\Node\BodyNode;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

class HintsNodeVisitor implements NodeVisitorInterface
{
    private string $varName;

    public function __construct(
        private readonly string $extensionName,
    ) {
        $this->varName = sprintf('__internal_%s', hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $extensionName));
    }

    public function enterNode(Node $node, Environment $env): Node
    {
        $templateName = $node->getTemplateName();
        if ($node instanceof ModuleNode) {
            $node->setNode('body', new BodyNode([
                new EnterModuleNode($this->extensionName, $templateName, $this->varName),
                $node->getNode('body'),
                new LeaveModuleNode($this->extensionName, $templateName, $this->varName),
            ]));
        } elseif ($node instanceof BlockNode) {
            $blockName = $node->getAttribute('name');
            // Note that we are skipping anything related to attributes, as we cannot put comments inside the html tags.
            if (
                'attributes' === $blockName
                || HintsExtension::str_ends_with($blockName, [
                    '_attributes',
                    '_attr',
                    '_class',
                    '_id',
                ])
            ) {
                return $node;
            }
            $node->setNode('body', new BodyNode([
                new EnterBlockNode($this->extensionName, $blockName, $templateName, $this->varName),
                $node->getNode('body'),
                new LeaveBlockNode($this->extensionName, $blockName, $templateName, $this->varName),
            ]));
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }
}
