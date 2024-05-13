<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Metric;

use DZunke\PanalyCodeOwners\Metric\OwnedFilesCount;
use DZunke\PanalyCodeOwners\Owner;
use DZunke\PanalyCodeOwners\Parser\Parser;
use DZunke\PanalyCodeOwners\PluginOptions;
use Panaly\Result\Metric\IntegerValue;
use PHPUnit\Framework\TestCase;

class OwnedFilesCountTest extends TestCase
{
    public function testThatTheIdentifierIsCorrect(): void
    {
        $metric = $this->getMetric();

        self::assertSame('owned_files_count', $metric->getIdentifier());
    }

    public function testThatTheDefaultTitleIsCorrect(): void
    {
        $metric = $this->getMetric();

        self::assertSame('Owned Files Count', $metric->getDefaultTitle());
    }

    public function testResultWithoutOwnersOption(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->never())->method('parse');

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate([]);

        self::assertInstanceOf(IntegerValue::class, $value);
        self::assertSame(0, $value->value);
    }

    public function testTableResultWithoutOwnersButWithOwnerOption(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())->method('parse')->willReturn([]);

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate(['owners' => ['@owner']]);

        self::assertInstanceOf(IntegerValue::class, $value);
        self::assertSame(0, $value->value);
    }

    public function testResultWithFiles(): void
    {
        $owner = self::createStub(Owner::class);
        $owner->method('getFiles')->willReturn(['foo', 'bar']);

        $parser = $this->createMock(Parser::class);
        $parser->expects($this->once())->method('parse')->willReturn(['@owner' => $owner]);

        $metric = $this->getMetric($parser);
        $value  = $metric->calculate(['owners' => ['@owner']]);

        self::assertInstanceOf(IntegerValue::class, $value);
        self::assertSame(2, $value->value);
    }

    private function getMetric(Parser|null $parser = null): OwnedFilesCount
    {
        return new OwnedFilesCount(
            $parser ?? self::createStub(Parser::class),
            PluginOptions::fromArray([
                'codeowners' => __DIR__ . '/../Fixture/CODEOWNERS_Github',
                'replace' => [['metric' => 'foo', 'option' => 'bar', 'owners' => ['@foo']]],
            ]),
        );
    }
}
