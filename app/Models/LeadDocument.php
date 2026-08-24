<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_documents';
    protected $primaryKey = 'lead_documents_id';

    protected $fillable = [
        'lead_id',
        'document_type',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    protected static function booted()
    {
        static::deleting(function ($document) {
            if (!empty($document->file_path)) {
                delete_file($document->file_path);
            }
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }

    /**
     * Scope lead documents accessible by a specific user (staff-wise data access).
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        $userId = $user->id;

        return $query->where(function ($q) use ($userId) {
            $q->where('uploaded_by', $userId)
              ->orWhereHas('lead', function ($lq) use ($userId) {
                  $lq->where('assigned_to', $userId)
                    ->orWhere('created_by', $userId);
              });
        });
    }
}
