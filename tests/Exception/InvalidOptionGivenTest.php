<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Test\Exception;

use DZunke\PanalyCodeOwners\Exception\InvalidOptionGiven;
use PHPUnit\Framework\TestCase;

class InvalidOptionGivenTest extends TestCase
{
    public function testCodeOwnersFileNotReadbale(): void
    {
        self::assertSame(
            'The given "codeowners" file "bar" does not exists or is not readable.',
            InvalidOptionGiven::codeOwnersFileNotReadable('bar')->getMessage(),
        );
    }

    public function testReplaceMetricOptionIsEmpty(): void
    {
        self::assertSame(
            'There should be at least a single metric configured within "replace" option',
            InvalidOptionGiven::replaceMetricOptionIsEmpty()->getMessage(),
        );
    }

    public function testMetricPathIsEmpty(): void
    {
        self::assertSame(
            'The path to the metric to be handled must not be empty.',
            InvalidOptionGiven::metricPathIsEmpty()->getMessage(),
        );
    }

    public function testMetricOptionIsEmpty(): void
    {
        self::assertSame(
            'The metric replacement information "option" must not be empty.',
            InvalidOptionGiven::metricOptionIsEmpty()->getMessage(),
        );
    }

    public function testAtLeastASingleOwnerGroupMustBeGiven(): void
    {
        self::assertSame(
            'At least a single owner must be mentioned within "owners" information.',
            InvalidOptionGiven::atLeastASingleOwnerGroupMustBeGiven()->getMessage(),
        );
    }
}
