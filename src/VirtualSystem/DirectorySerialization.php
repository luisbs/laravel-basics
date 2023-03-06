<?php

namespace Basics\VirtualSystem;

/**
 * @param \Basics\VirtualSystem\DirectorySerialization $folders
 */
trait DirectorySerialization
{
    /**
     * Serialize a fraction of the virtual filesystem in a nested structure. If a negative `$deepness`
     * is passed the deepness will be ignored and all nested elements included.
     *
     * TODO: test for performance
     */
    public static function serializeFoldersTree(VirtualElement $root, int $deepness = 1, bool $includeRoot = false): array
    {
        $tree = [];

        if ($deepness < 0) {
            // include all sub-elements ignoring deepness
            foreach ($root->folders as $folder) {
                $content = static::serializeFoldersTree($folder, false);
                $tree[] = [
                    'id' => $folder->id,
                    'path' => [$folder->name],
                    'content' => $content,
                ];
            }
        } else if ($deepness > 1) {
            // include a fraction of the sub-elements
            foreach ($root->folders as $folder) {
                $content = static::serializeFoldersTree($folder, $deepness - 1);
                $tree[] = [
                    'id' => $folder->id,
                    'path' => [$folder->name],
                    'content' => $content,
                ];
            }
        } else {
            // include only the direct sub-elements
            foreach ($root->folders as $folder) {
                $tree[] = [
                    'id' => $folder->id,
                    'path' => [$folder->name],
                    'content' => [],
                ];
            }
        }

        return $includeRoot ? [
            'id' => $root->id,
            'path' => $folder->name,
            'content' => $tree,
        ] : $tree;
    }

    /**
     * Serialize a fraction of the virtual filesystem in a flat array. If a negative
     * `$deepness`to ignore is passed the nd will be ignored include sub-nested included
     *
     * TODO: test for performance
     */
    public static function flatFoldersTree(VirtualElement $root, int $deepness = 1, bool $includeRoot = false, array $inherentPath = [], array $tree = []): array
    {
        if ($includeRoot) {
            $tree[$root->id] = array_merge($inherentPath);
        }

        if ($deepness < 0) {
            // include all sub-elements ignoring deepness
            foreach ($root->folders as $folder) {
                $path = array_merge($inherentPath, [$folder->name]);
                static::flatFoldersTree($folder, false, true, $path, $tree);
            }
        } else if ($deepness > 1) {
            // include a fraction of the sub-elements
            foreach ($root->folders as $folder) {
                $path = array_merge($inherentPath, [$folder->name]);
                static::flatFoldersTree($folder, $deepness - 1, true, $path, $tree);
            }
        } else {
            // include only the direct sub-elements
            foreach ($root->folders as $folder) {
                $tree[$folder->id] = array_merge($inherentPath, [$folder->name]);
            }
        }

        return $tree;
    }
}
