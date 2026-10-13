<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ticket_number',
    'user_id',
    'assigned_to',
    'subject',
    'category',
    'location',
    'department',
    'priority',
    'description',
    'status',
    'resolution_notes',
    'resolved_by',
    'resolved_at',
])]
class Ticket extends Model
{
    public const CATEGORIES = [
        'Network Issue',
        'Computer Hardware',
        'Software Issue',
        'Printer Issue',
        'Login / Account Issue',
        'Email Issue',
        'Server Issue',
        'System Access',
        'Other',
    ];

    public const PRIORITIES = ['Low', 'Medium', 'High', 'Critical'];

    public const STATUSES = ['Open', 'In Progress', 'Resolved', 'Closed'];

    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'In Progress' => 'text-bg-warning',
            'Resolved' => 'text-bg-success',
            'Closed' => 'text-bg-secondary',
            default => 'text-bg-primary',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'Critical' => 'text-bg-danger',
            'High' => 'text-bg-warning',
            'Medium' => 'text-bg-info',
            default => 'text-bg-secondary',
        };
    }
}
