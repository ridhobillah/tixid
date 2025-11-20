<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'date',
        'quantity',
        'total_price',
        'actived',
        'promo_id',
        'row_of_seats',
        'hours'
    ];

    protected function casts(): array
    {
        return [
            'row_of_seats' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);

    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function ticketPayment()
    {
        return $this->hasOne(TicketPayment::class);
    }

}
