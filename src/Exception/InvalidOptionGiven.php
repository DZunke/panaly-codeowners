<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Exception;

use InvalidArgumentException;

final class InvalidOptionGiven extends InvalidArgumentException
{
    public static function codeOwnersFileNotReadable(string $file): self
    {
        return new self('The given "codeowners" file "' . $file . '" does not exists or is not readable.');
    }

    public static function replaceMetricOptionIsEmpty(): self
    {
        return new self('There should be at least a single metric configured within "replace" option');
    }

    public static function metricPathIsEmpty(): self
    {
        return new self('The path to the metric to be handled must not be empty.');
    }

    public static function metricOptionIsEmpty(): self
    {
        return new self('The metric replacement information "option" must not be empty.');
    }

    public static function atLeastASingleOwnerGroupMustBeGiven(): self
    {
        return new self('At least a single owner must be mentioned within "owners" information.');
    }
}
