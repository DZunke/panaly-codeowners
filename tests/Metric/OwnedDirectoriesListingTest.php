<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Metric;

use DZunke\PanalyCodeOwners\Metric\OwnedDirectoriesListing;
use DZunke\PanalyCodeOwners\Owner;
use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions;
use Panaly\Result\Metric\Table;
use PHPUnit\Framework\TestCase;

class OwnedDirectoriesListingTest extends TestCase
{
    public function testThatTheIdentifierIsCorrect(): void
    {
        $metric = $this->getMetric();

        self::assertSame('owned_directories_list', $metric->getIdentifier());
    }

    public function testThatTheDefaultTitleIsCorrect(): void
    {
        $metric = $this->getMetric();

        self::assertSame('Owned Directories List', $metric->getDefaultTitle());
    }

    public function testResultWithoutOwnersOption(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->never())->method('parse');

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate([]);

        self::assertInstanceOf(Table::class, $value);
        self::assertSame([], $value->rows);
    }

    public function testTableResultWithoutOwnersButWithOwnerOption(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())->method('parse')->willReturn([]);

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate(['owners' => ['@owner']]);

        self::assertInstanceOf(Table::class, $value);
        self::assertSame([], $value->rows);
    }

    public function testResultWithDirectories(): void
    {
        $owner = self::createStub(Owner::class);
        $owner->method('getFiles')->willReturn(['foo', 'bar']);

        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())->method('parse')->willReturn(['@owner' => $owner]);

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate(['owners' => ['@owner']]);

        self::assertInstanceOf(Table::class, $value);
        self::assertSame([], $value->rows);
    }

    private function getMetric(Parser|null $parser = null): OwnedDirectoriesListing
    {
        return new OwnedDirectoriesListing(
            $parser ?? self::createStub(Parser::class),
            PluginOptions::fromArray([
                'codeowners' => __DIR__ . '/../Fixture/CODEOWNERS_Github',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@foo']]],
            ]),
        );
    }
}
