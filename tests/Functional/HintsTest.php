<?php

namespace LukaszZaroda\TwigHints\Tests\Functional;

use LukaszZaroda\TwigHints\Twig\HintsExtension;
use LukaszZaroda\TwigHints\Twig\Node\EnterBlockNode;
use LukaszZaroda\TwigHints\Twig\Node\EnterModuleNode;
use LukaszZaroda\TwigHints\Twig\Node\LeaveBlockNode;
use LukaszZaroda\TwigHints\Twig\Node\LeaveModuleNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

#[CoversClass(HintsExtension::class)]
#[CoversClass(EnterModuleNode::class)]
#[CoversClass(LeaveModuleNode::class)]
#[CoversClass(EnterBlockNode::class)]
#[CoversClass(LeaveBlockNode::class)]
class HintsTest extends TestCase
{
    public function testModuleHints()
    {
        $arrayLoader = new ArrayLoader();
        $templateName = 'test';
        $templateContent = 'Test template content';
        $arrayLoader->setTemplate($templateName, $templateContent);
        $env = new Environment($arrayLoader);
        $env->addExtension(new HintsExtension());
        $template = $env->load($templateName);
        $result = $template->render();
        $this->assertEquals("<!-- BEGIN module, $templateName -->$templateContent<!-- END module, $templateName -->", $result);
    }

    public function testBlockHints()
    {
        $arrayLoader = new ArrayLoader();
        $templateName = 'test';
        $blockName = 'testblock';
        $blockContent = 'block content';
        $arrayLoader->setTemplate($templateName, "{% block $blockName %}$blockContent{% endblock %}Test template content {{ block('$blockName') }}");
        $env = new Environment($arrayLoader);
        $env->addExtension(new HintsExtension());
        $template = $env->load($templateName);
        $result = $template->render();
        $this->assertStringContainsString("<!-- BEGIN block, $blockName, $templateName -->$blockContent<!-- END block, $blockName, $templateName -->", $result);
    }

    public function testNestedBlockHints()
    {
        $arrayLoader = new ArrayLoader();
        $templateName = 'test';
        $blockName = 'testblock';
        $nestedBlockName = 'nestedblock';
        $blockContent = 'block content';
        $nestedBlockContent = 'nested block content';
        $arrayLoader->setTemplate($templateName, "
        {% block $nestedBlockName %}$nestedBlockContent{% endblock %}
        {% block $blockName %}$blockContent{{ block('$nestedBlockName') }}{% endblock %}
        Test template content {{ block('$blockName') }}
        ");
        $env = new Environment($arrayLoader);
        $env->addExtension(new HintsExtension());
        $template = $env->load($templateName);
        $result = $template->render();
        $this->assertStringContainsString("<!-- --BEGIN block, $nestedBlockName, $templateName -->$nestedBlockContent<!-- --END block, $nestedBlockName, $templateName -->", $result);
    }

    public function testCSSJSModuleHints()
    {
        $arrayLoader = new ArrayLoader();
        $templateNames = ['test.css', 'test.css.twig', 'test.js', 'test.js.twig'];
        $templateContent = 'Test template content';
        foreach ($templateNames as $templateName) {
            $arrayLoader->setTemplate($templateName, $templateContent);
        }
        $env = new Environment($arrayLoader);
        $env->addExtension(new HintsExtension());
        foreach ($templateNames as $templateName) {
            $template = $env->load($templateName);
            $result = $template->render();
            $this->assertEquals("/* BEGIN module, $templateName */$templateContent/* END module, $templateName */", $result);
        }
    }

}
