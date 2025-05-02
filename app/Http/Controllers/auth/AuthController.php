<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /*
    *   Login menggunakan email dan password
    */
    public function index()
    {
        return view('auth.login');
    }

    public function prosesLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'required' => ':attribute wajib diisi!'
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            return redirect()->intended('/dashboard');
        }

        $this->incrementLoginAttempts($request);

        return back()->withErrors([
            'email' => 'Email atau Password Salah!',
        ])->onlyInput('email');
    }

    // Cek terlalu banyak percobaan login
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), 5);
    }

    // Kirim respons lockout
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        return redirect()->route('login')->withErrors([
            'email' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'
        ])->withInput($request->only('email'));
    }

    // Increment percobaan login
    protected function incrementLoginAttempts(Request $request)
    {
        RateLimiter::hit($this->throttleKey($request));
    }

    // Clear percobaan login
    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    // Key untuk throttling
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /*
    *   Login menggunakan OAuth Google
    */
    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        $email = $googleUser->email;

        // Pattern untuk memeriksa apakah alamat email berasal dari domain "uns.ac.id"
        $pattern = '/\b[A-Za-z0-9._%+-]+@student\.uns\.ac\.id\b/';

        if (!preg_match($pattern, $email)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gunakan email uns.ac.id untuk login',
            ]);
        }

        $registeredUser = User::where('google_id', $googleUser->id)->first();
        if (!$registeredUser) {
            return redirect()->route('google.registrasi')->with(['user' => $googleUser]);
        } else {
            return $this->login($registeredUser, $googleUser);
        }
    }

    private function login($user, $google = null)
    {
        $user->update([
            'avatar' => $google->avatar,
        ]);
        Auth::login($user, true);
        return redirect()->intended('/dashboard');
    }

    /*
    *   Registrasi
    */
    public function create(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('home');
        }
        $user = session('user');
        $prodi = Prodi::orderBy('name', 'desc')->get();

        return view('auth.register', [
            'user' => $user,
            'prodis' => $prodi,
        ]);
    }

    public function register(Request $request)
    {
        $validasi = Validator::make($request->input(), [
            'google_id' => ['required'],
            'name' => ['required', 'string'],
            'prodi' => ['required', 'not_in:1,2'],
            'foto' => ['required'],
            'nim' => ['required', 'regex:/^[a-zA-Z][0-9]+$/'],
            'no_wa' => ['required', 'numeric'],
        ], [
            'required' => ':attribute wajib diisi!',
            'regex' => ':attribute tidak valid.',
            'numeric' => ':attribute tidak valid.',
            'not_in' => ':attribute tidak valid.',
        ], [
            'name' => 'Nama Lengkap',
            'prodi' => 'Program Studi',
            'nim' => 'NIM',
            'no_wa' => 'Nomor WhatsApp',
        ]);

        if ($validasi->fails()) {
            $user = $this->setSession($request);
            $errors = $validasi->errors();
            return redirect()->back()->withErrors($errors)->withInput()->with(['user' => $user]);
        }

        $data = $request->input();
        $password = Str::random(8);
        $data['password'] = Hash::make($password);
        $data['role'] = '1';

        try {
            $user = User::create($data);
            return $this->login($user, $user);
        } catch (\Throwable $th) {
            return redirect()->route('home')->with('error', 'Terjadi Kesalahan Saat Proses Registrasi');
            // return redirect()->route('home')->with('error', $th->getMessage());
        }
    }

    private function setSession(Request $request)
    {
        $dataUser = json_decode(json_encode([
            'id' => $request->google_id,
            'avatar' => $request->foto,
            'name' => $request->name,
            'email' => $request->email,
        ]));
        return $dataUser;
    }
}