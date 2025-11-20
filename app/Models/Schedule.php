<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Schedule extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'cinema_id',
        'movie_id',
        'hours',
        'price'
    ];

    //memanggil relasi cinema, schedule mempunyai FK cinema
    // karena one ( cinema ) to many ( schedule ) : nama tunggal

    public function cinema()
    {
        // untuk table yang memegang FK gunakan ini
        return $this->belongsTo(Cinema::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function casts(): array
    {
        return [
            'hours' => 'array'
        ];
    }
}
