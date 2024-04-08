<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Exception;

use RuntimeException;
use Throwable;

final class FileParsingFailed extends RuntimeException
{
    public static function fileCouldNotBeParsed(string $file, Throwable $previous): FileParsingFailed
    {
        return new self(
            message: 'The file "' . $file . '" could not be parsed to owner configuration',
            previous: $previous,
        );
    }
}
