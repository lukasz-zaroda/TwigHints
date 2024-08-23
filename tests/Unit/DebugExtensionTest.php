<?php

namespace LukaszZaroda\TwigHints\Tests\Unit;

use LukaszZaroda\TwigHints\Twig\HintsExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HintsExtension::class)]
class DebugExtensionTest extends TestCase
{
    public function testNodeVisitors()
    {
        $extension = new HintsExtension();
        $nodeVisitors = $extension->getNodeVisitors();
        $this->assertIsArray($nodeVisitors);
        $this->assertNotEmpty($nodeVisitors);
    }
}
