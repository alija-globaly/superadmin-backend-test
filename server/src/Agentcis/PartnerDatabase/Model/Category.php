<?php

namespace Agentcis\PartnerDatabase\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'partner_label',
        'product_label',
        'type',
    ];

    /**
     * Relation to the parent.
     *
     * @return BelongsTo
     */
    public function master()
    {
        return $this->belongsTo(get_class($this), $this->getParentIdName())
            ->setModel($this);
    }
    public function scopeTypeFilter(Builder $query, $type): Builder
    {
        return $query->where('type', '=', $type);
    }
}
