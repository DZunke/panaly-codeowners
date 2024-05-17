<?php

declare(strict_types=1);

namespace DZunke\PanalyCodeOwners\Parser;

use CodeOwners\Exception\NoMatchFoundException;
use CodeOwners\Parser as CodeOwnerStandardParser;
use CodeOwners\PatternMatcher;
use DZunke\PanalyCodeOwners\Owner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\Finder;

use function array_key_exists;
use function assert;
use function getcwd;
use function is_string;
use function sha1;
use function str_contains;
use function trim;

class Parser
{
    public const UNOWNED = 'unowned';

    /** @var array<string, array<non-empty-string, Owner>> */
    private static array $ownerCache = [];

    private LoggerInterface $logger;
    private string $workingDirectory;

    public function __construct(
        string|null $workingDirectory = null,
        LoggerInterface|null $logger = null,
    ) {
        $this->workingDirectory = $workingDirectory ?? (string) getcwd();
        $this->logger           = $logger ?? new NullLogger();
    }

    /** @return array<non-empty-string, Owner> */
    public function parse(Configuration $configuration, string $definition): array
    {
        $codeownerContentHash = sha1($definition);
        if (isset(self::$ownerCache[$codeownerContentHash])) {
            $this->logger->debug('Loaded code owners from cache.');

            return self::$ownerCache[$codeownerContentHash];
        }

        $this->logger->debug('Parsing code owners from filesystem.');

        $patterns = (new CodeOwnerStandardParser())->parseString($definition);
        $matcher  = new PatternMatcher(...$patterns);
        $owners   = $this->patternsToOwners($patterns);

        $owners[self::UNOWNED] = new Owner(self::UNOWNED);

        $pathsIterator = (new Finder())
            ->in($this->workingDirectory)
            ->notPath($configuration->excludeDirectories)
            ->ignoreDotFiles($configuration->ignoreDotFiles)
            ->directories();

        foreach ($pathsIterator as $path => $pathInfo) {
            try {
                $foundOwners = $matcher->match($path);
            } catch (NoMatchFoundException) {
                $owners[self::UNOWNED]->addPath($pathInfo);
                continue;
            }

            foreach ($foundOwners->getOwners() as $foundOwner) {
                // All fine ... we do not need a match - has no owner
                $owners[$foundOwner]->addPath($pathInfo);
            }
        }

        $filesIterator = (new Finder())
            ->in($this->workingDirectory)
            ->notPath($configuration->excludeDirectories)
            ->ignoreDotFiles($configuration->ignoreDotFiles)
            ->files();

        foreach ($filesIterator as $file => $fileInfo) {
            try {
                $foundOwners = $matcher->match($file);
            } catch (NoMatchFoundException) {
                // All fine ... we do not need a match - has no owner
                $owners[self::UNOWNED]->addFile($fileInfo);
                continue;
            }

            foreach ($foundOwners->getOwners() as $foundOwner) {
                $owners[$foundOwner]->addFile($fileInfo);
            }
        }

        return self::$ownerCache[$codeownerContentHash] = $owners;
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
