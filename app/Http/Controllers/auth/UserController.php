<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\PembinaOrmawa;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.user.index');
    }

    public function list(Request $request)
    {
        $user = User::with('roles', 'prodis')->get();
        return DataTables::of($user)->addIndexColumn()
            ->editColumn('name', function ($row) {
                return wordwrap($row->name, 25,"<br/>");
            })
            ->editColumn('foto', function ($row) {
                $src = $row->foto ?? asset('back/assets/images/avatars/01.png');
                return '<img class="bg-soft-primary rounded img-fluid avatar-40 me-3" src="' . $src . '" alt="profile">';
            })
            ->addColumn('action', function ($row) {
                $action = '<a class="btn btn-sm btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit Profile" data-original-title="Edit" href="' . route('user.edit', encodeId($row->id)) . '">
                                <i class="fa fa-pen"></i>
                            </a>';
                $action .= '<button class="btn btn-sm btn-icon btn-danger btn-delete" title="Hapus Profile" data-id="' . encodeId($row->id) . '"><i class="fa fa-trash"></i></button>';
                return '<div class="flex align-item-center list-user-action">' . $action . '</div>';
            })
            ->editColumn('role', function ($row) {
                return $row->roles->name;
            })
            ->rawColumns(['name','foto', 'action'])
            ->toJson();
    }

    public function create()
    {
        $role = Role::all();
        // $prodi = Prodi::orderBy('name')->get();
        // $pembina = PembinaOrmawa::orderBy('name')->get();
        return view('pages.user.create', ['roles' => $role]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'nim' => ['required', 'string'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role' => Rule::requiredIf($request->user()->role == 2),
            'prodi' => ['exclude_unless:role,1', 'required', 'not_in:1'],
            'no_wa' => ['exclude_unless:role,1', 'required'],
            'pembina' => ['exclude_unless:role,5', 'required', 'not_in:1'],
            'pangkat' => ['exclude_unless:role,3', 'required', 'not_in:1'],
            'jabatan' => ['exclude_unless:role,3', 'required', 'not_in:1'],
        ], [
            'required' => ':attribute wajib diisi!',
            'required_if' => ':attribute wajib diisi!',
            'min' => 'Panjang :attribute minimal :min karakter',
            'email' => 'Email tidak valid',
            'not_in' => ':attribute tidak valid',
        ], [
            'name' => 'Nama',
            'email' => 'Email',
            'nim' => 'NIM/NIP',
            'password' => 'Password',
            'role' => 'Role',
            'prodi' => 'Program Studi',
            'no_wa' => 'Nomor Whatsapp',
        ]);


        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'nim' => $request->nim,
                'prodi' => $request->prodi,
                'password' => Hash::make($request->password),
                'no_wa' => $request->no_wa,
                'pangkat' => $request->pangkat,
                'jabatan' => $request->jabatan,
                'pembina_id' => $request->pembina,
            ]);
            return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Terjadi Kesalahan')->withInput();
        }
    }

    public function edit($id)
    {
        $id = decodeId($id);
        if (Auth::user()->role == '2' || Auth::user()->id == $id) {
            $user = User::findOrFail($id);
            $role = Role::all();
            $prodi = Prodi::where('id', '!=', '2')->orderBy('name')->get();
            $pembina = PembinaOrmawa::orderBy('name')->get();

            return view('pages.user.edit', ['user' => $user, 'roles' => $role, 'prodis' => $prodi, 'pembinas' => $pembina]);
        }
        abort(403);
    }

    public function update(Request $request, $id)
    {
        $id = decodeId($id);
        if (Auth::user()->role != 2 && Auth::user()->id != $id) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'nim' => ['required', 'string'],
            'role' => Rule::requiredIf(function () {
                return Auth::user()->role == 2;
            }),
            'prodi' => ['exclude_unless:role,1', 'required', 'not_in:1,2'],
            'no_wa' => ['exclude_unless:role,1', 'required'],
            'pembina' => ['exclude_unless:role,5', 'required', 'not_in:1'],
            'pangkat' => ['exclude_unless:role,3', 'required'],
            'jabatan' => ['exclude_unless:role,3', 'required'],
        ], [
            'required' => ':attribute wajib diisi!',
            'min' => 'Panjang :attribute minimal :min karakter',
            'email' => 'Email tidak valid',
            'not_in' => ':attribute tidak valid',
        ], [
            'name' => 'Nama',
            'nim' => 'NIM/NIP',
            'email' => 'Email',
            'role' => 'Role',
            'no_wa' => 'Nomor Whatsapp',
        ]);

        // Validasi hanya dijalankan jika password diisi
        $validator->sometimes('password', 'min:8|confirmed', function ($input) {
            return $input->password !== null;
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'pangkat' => $request->pangkat,
            'jabatan' => $request->jabatan,
            'no_wa' => $request->no_wa,
            'pembina_id' => $request->pembina,
        ];

        if (Auth::user()->role == '2') {
            $data['role'] = $request->role;
        }
        if ($request->password != null) {
            $data['password'] = $request->password;
        }

        try {
            User::findOrFail($id)->update($data);
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Terjadi Kesalahan')->withInput();
        }

        if (Auth::user()->role == '2') {
            return redirect()->route('user.index')->with('success', 'User berhasil diedit');
        }
        return redirect()->route('user.edit', encodeId($id))->with('success', 'Profil berhasil disimpan');
    }

    public function destroy($id)
    {
        $id = decodeId($id);
        try {
            User::findOrFail($id)->delete();
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'Terjadi Kesalahan'], 500);
        }
        return response()->json(['status' => true, 'message' => 'User berhasil dihapus'], 200);
    }
}
