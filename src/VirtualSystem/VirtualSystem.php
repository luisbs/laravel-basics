<?php

namespace Basics\VirtualSystem;

trait VirtualSystem
{
    /**
     * Takes an array of resourceIds and creates VirtualFiles
     * referencing the resources that doesn't have one already.
     */
    public static function addMissingResources(self $parent, string $resourceType, array $resourcesIds): void
    {
        foreach ($resourcesIds as $resourceId) {
            self::firstOrCreate([
                'owner_email' => $parent->owner_id,
                'resource_id' => $resourceId,
                'type'        => $resourceType,
            ], ['parent_id'   => $parent->id ]);
        }
    }

    /**
     * Return the root folder in the virtual filesystem
     * creating one if it is not found.
     */
    public static function getRootFolder($ownerId, string $resourceType): self
    {
        $folderName = self::getRootFolderName($resourceType);

        // look for the folder
        $folder = self::ownedBy($ownerId) //
            ->where([['parent_id', null], ['name', $folderName]])
            ->first();

        // if the folder was not found, creates a new one
        if (is_null($folder)) {
            $folder = static::createFolder($ownerId, null, $folderName);
            $folder->save();
        }

        return $folder;
    }

    /**
     * Return the name used to isolate the resources based on type.
     * If the same value is returned allways, all the resources are mixed.
     */
    public static function getRootFolderName(string $resourceType): string
    {
        return $resourceType;
    }

    /**
     * Creates a VirtualFolder in the virtual filesystem.
     */
    public static function createFolder($ownerId, $parentId, string $name): self
    {
        $folder = new self([
            'owner_id' => $ownerId,
            'parent_id' => $parentId,
            'name' => $name,
        ]);

        $folder->save();

        return $folder;
    }

    /**
     * Creates a VirtualFile in the virtual filesystem.
     */
    public static function createFile(self $parent, string $resourceType, string $resourceId): self
    {
        $file = new self([
            'owner_id' => $parent->owner_id,
            'parent_id' => $parent->id,
            'resource_id' => $resourceId,
            'type' => $resourceType,
        ]);

        $file->save();

        return $file;
    }
}
