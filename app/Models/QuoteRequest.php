<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $guarded = ['id'];

    public function files()
    {
        return $this->hasMany(QuoteRequestFile::class);
    }
}
