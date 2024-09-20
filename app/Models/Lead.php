<?php

namespace App\Models;

use App\Models\Enums\LeadStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Lead
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property string $appeal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LeadStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LeadStatus> $statuses
 * @property-read int|null $statuses_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereAppeal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lead whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'appeal',
    ];

    public function statuses(): HasMany
    {
        return $this->hasMany(LeadStatus::class, 'lead_id');
    }

    public function getStatusAttribute(): LeadStatusType
    {
        /** @var LeadStatus|null $status */
        $status = $this->statuses()->latest()->first();

        return $status?->type ?? LeadStatusType::New;
    }
}
