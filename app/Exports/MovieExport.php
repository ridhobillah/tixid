<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings; // untuk membuat th pada table excel //

use Maatwebsite\Excel\Concerns\WithMapping; // untuk membuat td pada table excel //

use Carbon\Carbon;

class MovieExport implements FromCollection, WithHeadings, WithMapping
{
    private $key = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Movie::all();
    }

    //menentukan isi th//
    public function headings(): array
    {
        return ['No', 'Judul', 'Durasi', 'Genre', 'Sutradara', 'Usia Minimal', 'Poster', 'Sinopsis', 'Status'];
    }

    //mengisi td//
    public function map($movie): array
    {
        return [
            ++$this->key,
            $movie->title,
            Carbon::parse($movie->duration)->format("H") . " Jam" .
            Carbon::parse($movie->duration)->format("i") . " Menit",
            $movie->genre,
            $movie->director,
            $movie->age_rating,
            //poster berupa url public : asset()
            asset('storage') . "/" . $movie->poster,
            $movie->description,
            //jika actived == 1 munculkan aktif, tidak non aktif
            $movie->actived == 1 ? 'Aktif' : 'Non-Aktif',

        ];
    }
}
