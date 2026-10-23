<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'rating',
        'message',
        'email',
        'source',
    ];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }
}
