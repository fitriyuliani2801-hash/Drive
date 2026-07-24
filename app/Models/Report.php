<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'user_id',
        'category_id',
        'reporter_name',
        'reporter_phone',
        'title',
        'description',
        'latitude',
        'longitude',
        'location_address',
        'district',
        'image_path',
        'status',
        'urgency',
        'admin_note',
        'resolution_image_path',
        'verified_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'verified_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReportLog::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak / Hoax',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
            'verified' => 'bg-blue-100 text-blue-800 border-blue-300',
            'in_progress' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function getUrgencyLabelAttribute(): string
    {
        return match ($this->urgency) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'critical' => 'Kritis / Darurat',
            default => 'Sedang',
        };
    }

    public function getUrgencyBadgeClassAttribute(): string
    {
        return match ($this->urgency) {
            'low' => 'bg-slate-100 text-slate-700',
            'medium' => 'bg-sky-100 text-sky-700',
            'high' => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-red-100 text-red-700 font-bold animate-pulse',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
