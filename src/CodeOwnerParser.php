<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners;

use CodeOwners\Parser;
use DZunke\PanalyCodeOwners\Exception\FileParsingFailed;
use Throwable;

use function array_key_exists;
use function assert;
use function is_string;
use function str_contains;
use function trim;

class CodeOwnerParser
{
    public function __construct(private readonly Parser $parser)
    {
    }

    /** @return array<string, Owner> */
    public function parse(string $filePath): array
    {
        try {
            $patterns = $this->parser->parseFile($filePath);
        } catch (Throwable $e) {
            throw FileParsingFailed::fileCouldNotBeParsed($filePath, $e);
        }

        $patternsGroupedByOwners = [];
        foreach ($patterns as $pattern) {
            // Exclude file patterns that matches gitlab sections as they are currently not supported
            if (str_contains($pattern->getPattern(), '[')) {
                continue;
            }

            $path = trim($pattern->getPattern(), '/');

            foreach ($pattern->getOwners() as $owner) {
                assert(is_string($owner));

                if (! array_key_exists($owner, $patternsGroupedByOwners)) {
                    $patternsGroupedByOwners[$owner] = new Owner($owner);
                }

                $patternsGroupedByOwners[$owner]->addPath($path);
            }
        }

        return $patternsGroupedByOwners;
    }
}
