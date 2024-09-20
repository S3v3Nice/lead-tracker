<?php

namespace App\Models;

use App\Models\Enums\LeadStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LeadStatus
 *
 * @property int $id
 * @property int $lead_id
 * @property LeadStatusType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lead $lead
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus whereLeadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
    ];

    protected $casts = [
        'type' => LeadStatusType::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
