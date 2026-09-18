<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = ['name', 'issuer', 'issued_at', 'credential_url', 'sort_order'];

    protected function casts(): array
    {
        return ['issued_at' => 'date'];
    }
}
