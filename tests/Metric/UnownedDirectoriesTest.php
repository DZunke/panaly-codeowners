<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Metric;

use DZunke\PanalyCodeOwners\Metric\UnownedDirectories;
use DZunke\PanalyCodeOwners\Owner;
use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions;
use Panaly\Result\Metric\Table;
use PHPUnit\Framework\TestCase;

class UnownedDirectoriesTest extends TestCase
{
    public function testThatTheIdentifierIsCorrect(): void
    {
        $metric = $this->getMetric();

        self::assertSame('unowned_directories', $metric->getIdentifier());
    }

    public function testThatTheDefaultTitleIsCorrect(): void
    {
        $metric = $this->getMetric();

        self::assertSame('Directories without Ownership', $metric->getDefaultTitle());
    }

    public function testTableResultWithoutOwners(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())->method('parse')->willReturn([]);

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate([]);

        self::assertInstanceOf(Table::class, $value);
        self::assertSame(['file'], $value->columns);
        self::assertSame([], $value->rows);
    }

    public function testTableResultWithFiles(): void
    {
        $owner = self::createStub(Owner::class);
        $owner->method('getRelativePaths')->willReturn(['foo', 'bar']);

        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())->method('parse')->willReturn([Parser::UNOWNED => $owner]);

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate([]);

        self::assertInstanceOf(Table::class, $value);
        self::assertSame(['file'], $value->columns);
        self::assertSame([['foo'], ['bar']], $value->rows);
    }

    private function getMetric(Parser|null $parser = null): UnownedDirectories
    {
        return new UnownedDirectories(
            $parser ?? self::createStub(Parser::class),
            PluginOptions::fromArray([
                'codeowners' => __DIR__ . '/../Fixture/CODEOWNERS_Github',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@foo']]],
            ]),
        );
    }
}
