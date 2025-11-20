<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MovieExport;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies= Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    public function chartData()
    {
        $movieActive = Movie::where('actived', 1)->count();
        $movieNonActive = Movie::where('actived', 0)->count();
        $data = [$movieActive, $movieNonActive];
        return response()->json([
            'data' => $data
        ]);
    }

    public function home()
    {
        $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->limit(4)->get();
        return view('home', compact('movies'));
    }

    public function homeAllMovies(Request $request)
    {
        $title = $request->search_movie;

        if ($title != "") {
            $movies = Movie::where('title','LIKE','%'.$title.'%')->where('actived', 1)->orderBy('created_at', 'DESC')->get();
        } else {
            $movies = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('home_movies', compact('movies'));
    }


    public function movieSchedules($movie_id, Request $request)
    {

        $priceSort = $request->price;

       if ($priceSort) {
            $movie = Movie::where('id', $movie_id)->with(['schedules' => function
            ($q) use ($priceSort) {
                $q->orderBy('price', $priceSort);
            }, 'schedules.cinema'])->first();
        } else {
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
        }

        return view('schedule.detail-film', compact('movie'));
    }

    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());//
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'age_rating' => 'required|numeric',
            // mimes -> bentuk file yang di izinkan untuk upload
            'poster' => 'required|mimes:jpg,jpeg,png,webp,svg',
            'description' => 'required|min:10'
        ], [
            'title.required' => 'judul film harus di isi',
            'duration.required' => 'durasi film harus diisi',
            'genre.required' => 'genre film harus diisi',
            'director.required' => 'sutradara film harus diisi',
            'age_rating.required' => 'usia minimal penonton harus diisi',
            'age_rating.numeric' => 'usia minimal penonton harus diisi dengan angka',
            'poster.required' => 'poster harus diisi',
            'poster.mimes' => 'poster harus diisi dengan JPG/JPEG/PNG/WEBP/SPG',
            'description.required' => 'sinopsis film harus diisi',
            'description.min' => 'sinopsis film harus diisi minimal 10 karakter',
        ]);
        $gambar = $request->file('poster');
        $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
        $path = $gambar->storeAs('poster', $namaGambar, 'public');

        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path, //path berisi lokasi file yang disimpan dari storeAs()
            'description' => $request->description,
            'actived' => 1
        ]);
        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil membuat data film!');
        }else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
     {
        //dd($request->all());//
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'age_rating' => 'required|numeric',
            // mimes -> bentuk file yang di izinkan untuk upload
            'poster' => 'mimes:jpg,jpeg,png,webp,svg',
            'description' => 'required|min:10'
        ], [
            'title.required' => 'judul film harus di isi',
            'duration.required' => 'durasi film harus diisi',
            'genre.required' => 'genre film harus diisi',
            'director.required' => 'sutradara film harus diisi',
            'age_rating.required' => 'usia minimal penonton harus diisi',
            'age_rating.numeric' => 'usia minimal penonton harus diisi dengan angka',
            'poster.mimes' => 'poster harus diisi dengan JPG/JPEG/PNG/WEBP/SPG',
            'description.required' => 'sinopsis film harus diisi',
            'description.min' => 'sinopsis film harus diisi minimal 10 karakter',
        ]);


        $movie = Movie::find($id);
        if($request->file('poster')) {
            $fileSebelumnya = storage_path('app/public/' . $movie['poster']);
            if (file_exists($fileSebelumnya)) {
                unlink($fileSebelumnya);
            }

            $gambar = $request->file('poster');
            $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
            $path = $gambar->storeAs('poster', $namaGambar, 'public');
        }


        $updateData = Movie::where('id', $id)->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            // ?? sebelum tanda tanya if sesudah tanda tanya else
            'poster' => $path ?? $movie['poster'], //path berisi lokasi file yang disimpan dari storeAs()
            'description' => $request->description,
            'actived' => 1
        ]);

        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengubah data film!');
        }else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{


     $schedules = Schedule::where('movie_id', $id)->count();
        if($schedules){
            return redirect()->route('admin.movies.index')->with('failed','Gagal menghapus data bioskop! Data tertaut dengan jadwal tayang');
        }



    $movie = Movie::findOrFail($id);

        // hapus poster dari storage
        $filePath = storage_path('app/public/' . $movie->poster);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // hapus data film
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus data film!');
    }

    public function nonAktif($id)
{
    $movie = Movie::findOrFail($id);

    $movie->update([
        'actived' => 0
    ]);

    return redirect()->route('admin.movies.index')->with('success', 'Film berhasil dinonaktifkan!');
    }

    public function export()
    {
        $fileName = 'data-film.xlsx';
        return Excel::download(new MovieExport, $fileName);
    }



      public function trash()
    {
        $movieTrash = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movieTrash'));
    }

    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->find( $id );
        $movie->restore();
        return redirect()->route('admin.movies.index')->with('success','Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->forceDelete();
        return redirect()->back()->with('success','Berhasil menghapus data secara permanen!');
    }



   public function datatables()
    {
        $movies = Movie::query();

        return DataTables::of($movies)
        ->addIndexColumn()
        ->addColumn('poster_img', function ($movie){
            $url = asset('storage/' . $movie->poster);
            return '<img src="' . $url . '" width="70">';
        })
        ->addColumn('actived_badge', function($movie){
            if($movie->actived){
                return '<span class="badge badge-success">Aktif</span>';
            } else{
                return '<span class="badge badge-danger">Non-Aktif</span>';
            }
        })
        ->addColumn('action', function($movie){
            $btnDetail = '<button type="button" class="btn btn-secondary" onclick= \'showModal(' . json_encode($movie) .')\'>Detail</button>';
            $btnEdit =  '<a href="' . route('admin.movies.edit', $movie->id) . '" class="btn btn-primary">Edit</a>';
            $btnDelete = ' <form action="' . route('admin.movies.delete', $movie->id) . '"  method="POST" style="display: inline-block;">
                            ' . csrf_field() . method_field('DELETE') .'
                            <button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
            $btnNonAktif = '';
            if($movie->actived){
                $btnNonAktif = ' <form action="' . route('admin.movies.nonaktif', $movie->id) . '"  method="POST" style="display: inline-block;">
                            ' . csrf_field() . method_field('PUT') .'
                            <button type="submit" class="btn btn-warning me-2">Non-Aktif</button>
                        </form>';
            }
            return '<div class="d-flex justify-content-center align-items-center gap-2">'. $btnDetail . $btnEdit . $btnDelete . $btnNonAktif .'</div>';
        })
        ->rawColumns(['poster_img', 'actived_badge', 'action'])
        ->make(true);
    }


}
