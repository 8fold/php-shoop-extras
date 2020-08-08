<?php

namespace Eightfold\ShoopExtras\Tests;

use PHPUnit\Framework\TestCase;

use Eightfold\ShoopExtras\Shoop;

use Eightfold\ShoopExtras\ESMarkdown;

use League\CommonMark\Extension\{
    GithubFlavoredMarkdownExtension,
    Autolink\AutolinkExtension,
    DisallowedRawHtml\DisallowedRawHtmlExtension,
    Strikethrough\StrikethroughExtension,
    Table\TableExtension,
    TaskList\TaskListExtension
};

use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkRenderer;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;

class MarkdownTest extends TestCase
{
    public function testCanGetContent()
    {
        $path = __DIR__ ."/data/inner-folder/subfolder/inner.md";
        $content = file_get_contents($path);

        $expected = "---\ntitle: Something\n---\n\nMarkdown text\n";
        $actual = ESMarkdown::fold($content)->main;
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

        $expected = "<i>Markdown content</i>";
        $actual = ESMarkdown::fold($content)->html([
            "text" => "content"
        ], [
            "<p>" => "<i>",
            "</p>" => "</i>"
        ]);
        $this->assertEquals($expected, $actual->unfold());
    }

    public function testExtensions()
    {
        $path = __DIR__ ."/data/table.md";

        $expected = "<p>|THead ||:-----||TBody |</p>";
        $actual = ESMarkdown::foldFromPath($path)->html();
        $this->assertSame($expected, $actual->unfold());

        $expected = '<table><thead><tr><th align="left">THead</th></tr></thead><tbody><tr><td align="left">TBody</td></tr></tbody></table>';
        $actual = ESMarkdown::foldFromPath($path, TableExtension::class)->html();
        $this->assertSame($expected, $actual->unfold());

        $path = __DIR__ ."/data/link.md";
        $expected = '<p><a rel="noopener noreferrer" target="_blank" href="https://github.com/8fold/php-shoop-extras">Something</a></p><p>Stripped</p>';
        $actual = ESMarkdown::foldFromPath($path)->extensions(
            ExternalLinkExtension::class
        )->html(
            [], [], true, true, [
                'html_input' => 'strip',
                "external_link" => [
                    "open_in_new_window" => true
                ]
            ]
        );
        $this->assertSame($expected, $actual->unfold());
    }
}
