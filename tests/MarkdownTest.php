<?php

namespace Eightfold\ShoopExtras\Tests;

use PHPUnit\Framework\TestCase;

use Eightfold\ShoopExtras\ESMarkdown;

class MarkdownTest extends TestCase
{
    public function testCanGetContent()
    {
        $path = __DIR__ ."/data/inner-folder/subfolder/inner.md";
        $content = file_get_contents($path);

        $expected = "---\ntitle: Something\n---\n\nMarkdown text\n";
        $actual = ESMarkdown::fold($content)->value;
        $this->assertEquals($expected, $actual);

        $actual = ESMarkdown::fold($content)->unfold();
        $this->assertSame($expected, $actual);
    }

    public function testCanGetParsed()
    {
        $path = __DIR__ ."/data/inner-folder/subfolder/inner.md";
        $content = file_get_contents($path);

        $expected = new \stdClass();
        $expected->title = "Something";
        $actual = ESMarkdown::fold($content)->meta;
        $this->assertEquals($expected, $actual);

        $expected = "\nMarkdown content\n";
        $actual = ESMarkdown::fold($content)->content(["text" => "content"]);
        $this->assertEquals($expected, $actual->unfold());

        $expected = "<i>Markdown content</i>";
        $actual = ESMarkdown::fold($content)->html([
            "text" => "content"
        ], [
            "<p>" => "<i>",
            "</p>" => "</i>"
        ]);
        $this->assertEquals($expected, $actual->unfold());
    }
}