<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Cinema;
use App\Models\Movie;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedule.index', compact('cinemas','movies','schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            'hours.*' => 'required'
        ], [
            'cinema_id.required' => 'Bioskop harus di pilih',
            'movie_id.required' => 'Film harus dipilih',
            'price.required'=> 'Harga harus diisi',
            'price.numeric'=> 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam harus diisi minimal satu data jam'

        ]);

        $schedule = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->first();
        if ($schedule) {
            $hours = $schedule['hours'];
        } else {
            $hours = [];
        }

        $mergeHours = array_merge($hours, $request->hours);

        $newHours = array_unique($mergeHours);

        $createData = Schedule::updateOrCreate([
            'cinema_id' => $request->cinema_id,
            'movie_id'=> $request->movie_id,
        ],[
            'price'=> $request->price,
            'hours'=> $newHours,
        ]);
        if ($createData) {
            return redirect()->route('staff.schedules.index')->with('success','Berhasil menambah data');
        } else {
            return redirect()->back()->with('error','Gagal! coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule, $id)
    {
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i',
        ], [
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang harus diisi',
            'hours.*.date_format' => 'Jam tayang harus diisi dengan format jam:menit',
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price'=> $request->price,
            'hours'=> $request->hours,
        ]);

        if($updateData) {
            return redirect()->route('staff.schedules.index')->with('success','Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error','Gagal mengubah data!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, $id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success','Berhasil menghapus data');
    }

    public function trash()
    {
        $scheduleTrash = Schedule::with(['cinema','movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }

    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->find( $id );
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('success','Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success','Berhasil menghapus data secara permanen!');
    }


    public function datatables()
    {
        $schedules = Schedule::with(['cinema' , 'movie'])->get();

        return DataTables::of($schedules)
            ->addIndexColumn()
            ->addColumn('cinema', function ($schedules) {
                return $schedules->cinema ? $schedules->cinema->name : '-';
            })
            ->addColumn('movie', function($schedules) {
                return $schedules->movie ? $schedules->movie->title : '-';
            })
            ->addColumn('price', function($schedules){
                return 'Rp ' . number_format($schedules->price, 0, ',', '.');
            })
            ->addColumn('hours', function ($schedules){
                if (!$schedules->hours) return '-';
                $hours = is_array($schedules->hours) ? $schedules->hours : explode (',' , $schedules->hours);
                $html = '<ul>';
                foreach ($hours as $hours) {
                    $html .= "<li>{$hours}</li>";
                }
                $html .= '</ul>';
                return $html;
            })
            ->addColumn('action', function ($schedules) {
                $btnEdit = '<a href="' . route('staff.schedules.edit', $schedules->id) . '" class="btn btn-primary me-1">Edit</a>';
                $btnDelete = '<form action="' . route('staff.schedules.delete', $schedules->id) . '" method="POST" style="display:inline-block">'
                    . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger me-1">Hapus</button>
                </form>';

                return '<div class="d-flex justify-content-center align-items-center flex-wrap">'
                    . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['hours', 'action'])
            ->make(true);
    }


}


