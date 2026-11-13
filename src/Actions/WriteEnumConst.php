<?php

namespace Rikthepixel\ModelTyper\Actions;

use ReflectionClass;

class WriteEnumConst
{
    /**
     * Write the enum const to the output.
     *
     * @return array{type: string, name: string}|string
     */
    public function __invoke(ReflectionClass $reflection, string $indent = '', bool $jsonOutput = false, bool $global = false): array|string
    {
        $entry = '';

        $docBlock = $reflection->getDocComment();

        $comments = [];
        $docBlock = $reflection->getDocComment();
        if ($docBlock) {
            $pattern = "#(@property+\s*[a-zA-Z0-9, ()_].*)#";
            preg_match_all($pattern, $docBlock, $matches, PREG_PATTERN_ORDER);
            $comments = array_map(fn ($match) => trim(str_replace('@property', '', $match)), $matches[0]);
        }

        $cases = collect($reflection->getConstants())
            ->filter(fn ($case) => $case instanceof \BackedEnum);

        if ($cases->isNotEmpty()) {
            $assignmentSign = $global ? ': ' : ' = ';
            $entry .= "{$indent}export const {$reflection->getShortName()}{$assignmentSign}{" . PHP_EOL;

            $cases->each(function ($case) use ($global, $indent, &$entry, $comments) {
                $name = $case->name;
                $value = is_string($case->value) ? "'" . addslashes($case->value) . "'" : $case->value;

                // write comments if they exist
                if (! empty($comments)) {
                    foreach ($comments as $comment) {
                        if (str_starts_with($comment, $name)) {
                            $comment = str_replace($name, '', $comment);
                            $comment = preg_replace('/[^a-zA-Z0-9\s]/', '', $comment);
                            $comment = trim($comment);
                            $entry .= "{$indent}  /** $comment */" . PHP_EOL;
                            break;
                        }
                    }
                }

                $entry .= "{$indent}  {$name}: {$value}," . PHP_EOL;
            });

            $as_const = $global ? "" : " as const" ;
            $entry .= "{$indent}}{$as_const};" . PHP_EOL;

            if (!$global) {
                $entry .= PHP_EOL . "{$indent}export type {$reflection->getShortName()} = typeof {$reflection->getShortName()}[keyof typeof {$reflection->getShortName()}]" . PHP_EOL . PHP_EOL;
            }
        }

        if ($jsonOutput) {
            return [
                'name' => $reflection->getShortName(),
                'type' => $entry,
            ];
        }

        return $entry;
    }
}
