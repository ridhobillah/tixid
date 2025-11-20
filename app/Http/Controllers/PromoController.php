<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PromoExport;
use Yajra\DataTables\Facades\DataTables;
class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|unique:promos',
            'discount' => 'required|numeric',
            'type' => 'required|in:percent,rupiah',
        ], [
            'promo_code.required' => 'Kode promo harus diisi.',
            'promo_code.unique'   => 'Kode promo sudah digunakan, silakan pakai kode lain.',
            'discount.required'   => 'Diskon harus diisi.',
            'discount.numeric'    => 'Diskon harus berupa angka.',
            'type.required'       => 'Jenis diskon harus dipilih.',
            'type.in'             => 'Jenis diskon hanya boleh percent atau rupiah.',
        ]);

        //kirim data
        $createPromo = Promo::create([
            'promo_code' => $request->promo_code,
            'discount' => $request->discount,
            'type' => $request->type,
            'actived' => 1
        ]);

        //redirect / perpindahan halaman
        if ($createPromo) {
            return redirect()->route('staff.promos.index')->with('success', 'Berhasil membuat data promo!');
        } else {
            return redirect()->back()->with('failed', 'Gagal membuat data promo');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promo = Promo::find($id);
        return view('staff.promo.edit', compact('promo'));
    }

    /**
     *]Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
         $request->validate([
             'promo_code' => 'required|unique:promos',
            'discount' => 'required|numeric',
            'type' => 'required|in:percent,rupiah',
        ], [
            'promo_code.required' => 'Kode promo harus diisi.',
            'promo_code.unique'   => 'Kode promo sudah digunakan, silakan pakai kode lain.',
            'discount.required'   => 'Diskon harus diisi.',
            'discount.numeric'    => 'Diskon harus berupa angka.',
            'type.required'       => 'Jenis diskon harus dipilih.',
            'type.in'             => 'Jenis diskon hanya boleh percent atau rupiah.',
         ]);

         $updatePromo = Promo::where('id', $id)->update([
            'promo_code' => $request->promo_code,
            'discount' => $request->discount,
            'type' => $request->type,
            'actived' => 1
         ]);

         if ($updatePromo) {
            return redirect()->route('staff.promos.index')->with('success','Berhasil mengedit data promo!');
         } else {
            return redirect()->back()->with('failed', 'Gagal mengedit data promo!');
         }
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy($id)
    {
        $deletePromo = Promo::where('id', $id)->delete();

        if( $deletePromo ) {
            return redirect()->route('staff.promos.index')->with('success','Berhasil menghapus data!');
        } else {
            return redirect()->back()-with('failed', 'Gagal menghapus data pengguna!');
        }
    }

    public function export()
    {
        $fileName = 'data-Promo.xlsx';
        return Excel::download(new PromoExport, $fileName);
    }

    public function nonactived($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->update([
            'actived' => 0
        ]);

        return redirect()->route('staff.promos.index')->with('success','Promo dinonaktifkan');
    }

    public function trash()
    {
        $promoTrash = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promoTrash'));
    }

    public function restore($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->restore();
        return redirect()->route('staff.promos.index')->with('success','Berhasil mengembalikan data');
    }

    public function deletePermanent($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->forceDelete();
        return redirect()->back()->with('success','Berhasil menghapus data secara permanen!');
    }


    public function datatables()
    {
        $promos = Promo::query();
        return DataTables::of($promos)
        ->addIndexColumn()
        ->addColumn('discount', function($promo){
            if ($promo['type'] == 'rupiah') {
                return '<span>Rp. ' . number_format($promo['discount'], 0, ',', ',') . '</span>';
            } else {
                return '<span>' . $promo['discount'] . ' %</span>';
            }
        })
        ->addColumn('type', function($promo){
            $type = $promo->type;
            return '<td>' . $type . '</td>';
        })
        ->addColumn('actived', function($promo){
            if($promo->actived){
                return '<span class="badge badge-success">Aktif</span>';
            } else{
                return '<span class="badge badge-danger">Non-Aktif</span>';
            }
        })

        ->addColumn('action', function($promo){
            $btnEdit =  '<a href="' . route('staff.promos.edit', $promo->id) . '" class="btn btn-primary">Edit</a>';
            $btnDelete = ' <form action="' . route('staff.promos.delete', $promo->id) . '" method="POST" style="display: inline-block;">
                            ' . csrf_field() . method_field('DELETE') .'
                            <button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';

            $btnNonAktif = '';
            if($promo->actived){
                $btnNonAktif = ' <form action="' . route('staff.promos.nonactived', $promo->id) . '"  method="POST" style="display: inline-block;">
                            ' . csrf_field() . method_field('PUT') .'
                            <button type="submit" class="btn btn-warning me-2">Non-Aktif</button>
                        </form>';
            }
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
        })
        ->rawColumns(['promo_code', 'discount', 'type', 'actived' , 'action'])
        ->make(true);
    }
}
