<?php

namespace LukaszZaroda\TwigHints\Twig;

use Twig\Extension\AbstractExtension;

class HintsExtension extends AbstractExtension
{
    private int $blockNestingLevel = 0;

    public function enterBlock(string $blockName, string $templateName): string
    {
        $output = $this->wrapBlockComment(
            "BEGIN block, $blockName, $templateName",
        );

        ++$this->blockNestingLevel;

        return $output;
    }

    public function leaveBlock(string $blockName, string $templateName): string
    {
        --$this->blockNestingLevel;

        return $this->wrapBlockComment(
            "END block, $blockName, $templateName",
        );
    }

    public function enterModule(string $templateName): string
    {
        return $this->wrapModuleComment(
            "BEGIN module, $templateName",
            $templateName
        );
    }

    public function leaveModule(string $templateName): string
    {
        return $this->wrapModuleComment(
            "END module, $templateName",
            $templateName,
        );
    }

    public function getNodeVisitors(): array
    {
        return [
            new HintsNodeVisitor(static::class),
        ];
    }

    /**
     * @param string[] $needles
     */
    public static function str_ends_with(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function wrapModuleComment(string $comment, string $templateName): string
    {
        return match (true) {
            $this->str_ends_with($templateName, [
                '.css',
                '.css.twig',
                'js',
                '.js.twig',
            ]) => '/* '.$comment.' */',
            default => $this->wrapInHtmlComment($comment),
        };
    }

    private function wrapBlockComment(string $comment): string
    {
        $prefix = str_repeat('--', $this->blockNestingLevel);

        return $this->wrapInHtmlComment("$prefix$comment");
    }

    private function wrapInHtmlComment(string $text): string
    {
        return "<!-- $text -->";
    }
}
