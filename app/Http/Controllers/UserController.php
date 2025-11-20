<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::all();
        return view('admin.users.index', compact('users'));
        //compact -> argumen pada fungsi akakn sama dengan nama variabel yang akna dikirim ke blade
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
        //
        //validasi
        $request->validate([
            'name' => 'required|min:3',
            //email:dns => emailnya valid, @gmail @company.co dsb
            'email' => 'required|email:dns',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama belakang wajib diisi',
            'name.min' => 'Nama belakang minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email wajib diisi dengan data yang valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',

        ]);

        //kirim data
        $createUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //pengguna tidak bisa memilih role {akses}, jadi manual ditambahkan 'user'
            'role' => 'staf'
        ]);

        //redirect / perpindahan halaman
        if ($createUser) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil membuat data bioskop!');
        } else {
            return redirect()->back()->with('failed', 'Gagal membuat data bioskop');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $User, $id)
    {
        //validasi
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama belakang wajib diisi',
            'name.min' => 'Nama belakang minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email wajib diisi dengan data yang valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',

        ]);

        //update data
            $updateUser = User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        //redirect / perpindahan halaman
        if ($updateUser) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil mengupdate data Pengguna!');
        } else {
            return redirect()->back()->with('failed', 'Gagal mengupdate data Pengguna');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleteData = User::where('id', $id)->delete();

        if ($deleteData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil menghapus data pengguna!');
        } else{
            return redirect()->back()-with('failed', 'Gagal menghapus data pengguna!');
        }
    }

    public function signUp(Request $request)
    {
        //{Requst $requset} : untuk mengambil data dari formulir
        //validasi data yang dikirim dari formulir
        $request->validate([
            //'name_input => 'tipe validasi'
            //required : harus diisi, min: minimal karakter (teks)
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            //email:dns => emailnya valid, @gmail @company.co dsb
            'email' => 'required|email:dns',
            'password' => 'required|min:8', // |max:20 untuk maksimal karakter
        ], [
            //pesan error custom
            //'name_input.validasi' => 'pesan'
            'first_name.required' => 'Nama depan wajib diisi',
            'first_name.min' => 'Nama depan minimal 3 karakter',
            'last_name.required' => 'Nama belakang wajib diisi',
            'last_name.min' => 'Nama belakang minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email wajib diisi dengan data yang valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        //create() : membuat data baru
        $createUser = User::create([
            //'nama_column' => $request->nama_input
            'name' => $request->first_name . "" . $request->last_name,
            'email' => $request->email,
            //Hash : enskripsi data (mengubah menjadi karakter acak) agar tidak ada yang bisa menebak isinya
            'password' => Hash::make($request->password),
            //pengguna tidak bisa memilih role {akses}, jadi manual ditambahkan 'user'
            'role' => 'user'
        ]);

        if ($createUser) {
            //redirec() : memindah halaman, route() : nama routing yang dituju
            //with() : mengirimkan session, biasanya untuk notifikasi
            return redirect()->route('login')->with('success', 'Akun berhasil dibuat, silakan login');
        } else {
            //back() : kembali ke halaman sebelumnya
            return redirect()->back()->with('error', 'Akun gagal dibuat, silahkan coba lagi');
        }
    }

    public function loginAuth(Request $request)
    {
        //validasi data yang dikirim dari formulir
        $request->validate([
            //'name_input => 'tipe validasi'
            //required : harus diisi, min: minimal karakter (teks)
            'email' => 'required',
            'password' => 'required', // |max:20 untuk maksimal karakter
        ], [
            //pesan error custom
            //'name_input.validasi' => 'pesan'
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        //mengambil data yang akan di verif
        $data = $request->only('email', 'password');
        //Auth:: => class laravel untuk otentikasi
        //attempt() : method class auth untuk mencocokan email dan pw atau username dan kalau cocok akan disimpan datanya ke sesssion auth
        if (Auth::attempt($data)) {
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login!');
            } elseif (Auth::user()->role == 'staf') {
                return redirect()->route('staff.dashboard')->with('success', 'Berhasil login!');
            } else {
                return redirect()->route('home')->with('success', 'Berhasil login');
            }
            //jika cocok
            //redirect ke halaman yang di tuju
        } else {
            //jika tidak cocok
            return redirect()->back()->with('error', 'Login gagal, email atau password tidak sesuai!');
        }
    }

     public function export()
    {
        $fileName = 'data-User.xlsx';
        return Excel::download(new UserExport, $fileName);
    }

    public function logout()
    {
        //logout : menghapus session auth
        Auth::logout();
        //redirect ke halaman login
        return redirect()->route('home')->with('logout', 'Berhasil logout! Silahkan login kembali untuk akses lengkap');
    }


    public function trash()
    {
        $userTrash = User::onlyTrashed()->get();
        return view('admin.users.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find( $id );
        $user->restore();
        return redirect()->route('admin.users.index')->with('success','Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $User = User::onlyTrashed()->find($id);
        $User->forceDelete();
        return redirect()->back()->with('success','Berhasil menghapus data secara permanen!');
    }

   public function datatables()
    {
        $users = User::query();
        $users = User::orderBy('role', 'asc')->get();
        return DataTables::of($users)
        ->addIndexColumn()
        ->addColumn('role', function($user) {
            if ($user['role'] == 'admin'){
                return '<span class="badge bg-success">admin</span>';
            } elseif ($user['role'] == 'staf'){
                return '<span class="badge bg-primary">staff</span>';
            }else{
                return '<span class="badge bg-secondary">User</span>';
            }
        })
        ->addColumn('action', function($user){
            $btnEdit =  '<a href="' . route('admin.users.edit', $user->id) . '" class="btn btn-primary">Edit</a>';
            $btnDelete = ' <form action="' . route('admin.users.delete', $user->id) . '" method="POST" style="display: inline-block;">
                            ' . csrf_field() . method_field('DELETE') .'
                            <button type="submit" class="btn btn-danger me-2">Hapus</button>
                        </form>';
            return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnEdit . $btnDelete . '</div>';
        })
        ->rawColumns(['email', 'role', 'action'])
        ->make(true);
    }

}
