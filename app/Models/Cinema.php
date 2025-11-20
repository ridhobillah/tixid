<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    //mendaftarkan softdelete
    use SoftDeletes;
    //mendaftarkan nama-column yang akan diisi, nama column selain id dan timetamp
    protected $fillable = ['name', 'location'];



    //fungsi relasi, karena one to many, nama nya jamak//
    public function schedules()
    {
        // pendefinisian jenis relasi ( one to one / one to many)//
        return $this->hasMany(Schedule::class);

    }
}
