<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Clipboard extends Model
{
    use HasFactory;

    protected $guarded = [];

//    relationships

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
