<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Parser;

use CodeOwners\Exception\NoMatchFoundException;
use CodeOwners\Parser as CodeOwnerStandardParser;
use CodeOwners\PatternMatcher;
use DZunke\PanalyCodeOwners\Owner;
use Symfony\Component\Finder\Finder;

use function array_key_exists;
use function assert;
use function is_string;
use function str_contains;
use function trim;

class Parser
{
    /** @return array<non-empty-string, Owner> */
    public function parse(Configuration $configuration, string $definition): array
    {
        $patterns = (new CodeOwnerStandardParser())->parseString($definition);
        $matcher  = new PatternMatcher(...$patterns);
        $owners   = $this->patternsToOwners($patterns);

        $pathsIterator = (new Finder())
            ->in($configuration->rootPath)
            ->notPath(['vendor'])
            ->ignoreDotFiles($configuration->ignoreDotFiles)
            ->directories();

        foreach ($pathsIterator as $path => $pathInfo) {
            try {
                $foundOwners = $matcher->match($path);
            } catch (NoMatchFoundException) {
                // All fine ... we do not need a match - has no owner
                continue;
            }

            foreach ($foundOwners->getOwners() as $foundOwner) {
                $owners[$foundOwner]->addPath($pathInfo);
            }
        }

        $filesIterator = (new Finder())
            ->in($configuration->rootPath)
            ->notPath(['vendor'])
            ->ignoreDotFiles($configuration->ignoreDotFiles)
            ->files();

        foreach ($filesIterator as $file => $fileInfo) {
            try {
                $foundOwners = $matcher->match($file);
            } catch (NoMatchFoundException) {
                // All fine ... we do not need a match - has no owner
                continue;
            }

            foreach ($foundOwners->getOwners() as $foundOwner) {
                $owners[$foundOwner]->addFile($fileInfo);
            }
        }

        return $owners;
    }

    /** @return array<non-empty-string, Owner> */
    private function patternsToOwners(array $patterns): array
    {
        $patternsGroupedByOwners = [];
        foreach ($patterns as $pattern) {
            // Exclude file patterns that matches gitlab sections as they are currently not supported
            if (str_contains($pattern->getPattern(), '[')) {
                continue;
            }

            $pathPattern = trim($pattern->getPattern(), '/');

            foreach ($pattern->getOwners() as $owner) {
                assert(is_string($owner) && $owner !== '');

                if (! array_key_exists($owner, $patternsGroupedByOwners)) {
                    $patternsGroupedByOwners[$owner] = new Owner($owner);
                }

                $patternsGroupedByOwners[$owner]->addPattern($pathPattern);
            }
        }

        return $patternsGroupedByOwners;
    }
}
