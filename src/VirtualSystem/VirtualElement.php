<?php

namespace Basics\VirtualSystem;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $owner
 * @property \Basics\VirtualSystem\VirtualElement $parent
 * @property \Basics\VirtualSystem\VirtualElement $content
 * @property \Basics\VirtualSystem\VirtualElement $folders
 * @property \Basics\VirtualSystem\VirtualElement $files
 * @property mixed $resource
 * @method static \Illuminate\Database\Eloquent\Builder ownedBy(ownerId)
 */
abstract class VirtualElement extends Model
{
    use VirtualSystem, DirectorySerialization;

    /**
     * The table associated with the model.
     */
    protected $table = 'virtual_filesystem';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        // 'id',
        'owner_id',
        'parent_id',
        'resource_id',
        'type',
        'name',
    ];

    /**
     * Checks if the current item is a VirtualFolder.
     */
    public function isFolder(): bool
    {
        return (bool) is_null($this->resource_id);
    }

    /**
     * Checks if the current item is a VirtualFolder.
     */
    public function isFile(): bool
    {
        return !$this->isFolder();
    }

    /**
     * Get the owner of this VirtualElement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public abstract function owner();

    /**
     * Get the parent VirtualFolder of this VirtualElement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Get the VirtualElement (folders and files) inside this VirtualFolder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Get the VirtualFolders inside this VirtualFolder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->content()->whereNull('resource_id');
    }

    /**
     * Get the VirtualFiles inside this VirtualFolder.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->content()->whereNotNull('resource_id');
    }

    /**
     * Get the resource this VirtualFile references.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public abstract function resource();

    /**
     * Filter model by ownerId.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwnedBy($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }
}
