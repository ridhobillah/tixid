<?php

namespace App\Exports;

use App\Models\Promo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PromoExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Promo::all();
    }

    public function headings(): array
    {
       return ['No', 'Kode Promo', 'Total Potongan'];
    }

    public function map($promo): array
    {
     return [
            ++$this->key,
            $promo->promo_code,
            $promo->type == 'percent' ? $promo->discount . '%' : 'Rp' . number_format($promo->discount,0,',','.')
        ];
    }
}
