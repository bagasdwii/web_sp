<?php

namespace App\Http\Controllers\Auth;

use App\Models\Nip;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Direksi;

use App\Models\Jabatan;
use App\Models\Nasabah;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use App\Models\PegawaiAdminKas;
use App\Models\SuratPeringatan;
use Illuminate\Validation\Rules;
use App\Models\PegawaiSupervisor;
use Illuminate\Routing\Controller;
use App\Models\PegawaiKepalaCabang;
use Illuminate\Support\Facades\Log;
use App\Models\PegawaiAccountOffice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\CabangWilayah; // Tambahkan model CabangWilayah

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $cabangs = \App\Models\Cabang::all(); // Mengambil semua cabang dari model Cabang

        return view('auth.register', compact('cabangs'));
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Debug: Log request data
        \Log::info('Request Data:', $request->all());

        // Common validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'jabatan_id' => ['required', 'integer', 'between:1,5'], // Validasi jabatan_id antara 1 hingga 5
        ];

        // Add specific validation rules based on jabatan_id
        switch ($request->jabatan_id) {
            case 2:
            case 3:
            case 4:
            case 5:
                $rules['id_cabang'] = ['required', 'integer'];
                break;
        }

        // Validate input data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan_id' => $request->jabatan_id
        ]);

        if (!$user) {
            throw ValidationException::withMessages(['error' => 'Failed to create user']);
        }

        // Save specific data based on jabatan_id
        switch ($request->jabatan_id) {
            case 1: // Direksi
                Direksi::create([
                    'nama' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                break;
            case 2: // Pegawai Kepala Cabang
                // Get id_direksi from the Direksi table
                $direksi = Direksi::first(); // Adjust this to your specific requirements
                PegawaiKepalaCabang::create([
                    'nama_kepala_cabang' => $request->name,
                    'id_jabatan' => $request->jabatan_id,
                    'id_cabang' => $request->id_cabang,
                    'id_direksi' => $direksi ? $direksi->id_direksi : null,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                break;
            case 3: // Pegawai Admin Kas
                PegawaiAdminKas::create([
                    'nama_admin_kas' => $request->name,
                    'id_supervisor' => $request->id_supervisor,
                    'id_jabatan' => $request->jabatan_id,
                    'id_cabang' => $request->id_cabang,
                    'id_wilayah' => $request->id_wilayah,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                break;
            case 4: // Pegawai Supervisor
                $kepalacabang = PegawaiKepalaCabang::first();
                $cabangWilayah = CabangWilayah::where('id_cabang', $request->id_cabang)->first();
                $id_wilayah = $cabangWilayah ? $cabangWilayah->id_wilayah : null;
                PegawaiSupervisor::create([
                    'nama_supervisor' => $request->name,
                    'id_kepala_cabang' => $kepalacabang ? $kepalacabang->id_kepala_cabang : null,
                    'id_jabatan' => $request->jabatan_id,
                    'id_cabang' => $request->id_cabang,
                    'id_wilayah' => $id_wilayah,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                break;
            case 5: // Pegawai Account Office
                PegawaiAccountOffice::create([
                    'nama_account_officer' => $request->name,
                    'id_admin_kas' => $request->id_admin_kas,
                    'id_jabatan' => $request->jabatan_id,
                    'id_cabang' => $request->id_cabang,
                    'id_wilayah' => $request->id_wilayah,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                break;
            default:
                break;
        }

        // Event registered user
        event(new Registered($user));

        // Autentikasi user
        Auth::login($user);

        // Redirect home
        return redirect(RouteServiceProvider::HOME);


    }
    public function index(Request $request)
    {
        return $request->user();
    }
    public function getUserDetails(Request $request)
    {
        $user = $request->user(); // Mendapatkan user yang sedang login
        $jabatan = $user->jabatan;
    
        // Log informasi awal
        Log::info('Fetching user details for user: ' . $user->id);
        Log::info('Fetching jabatan details for user: ' . $jabatan);
    
        // Eager load relasi yang diperlukan berdasarkan jabatan
        if ($jabatan->id_jabatan == 2) {
            $user->load('pegawaiKepalaCabang.cabang', 'pegawaiKepalaCabang.direksi');
        } else if ($jabatan->id_jabatan == 3) {
            $user->load('pegawaiSupervisor.cabang', 'pegawaiSupervisor.wilayah', 'pegawaiSupervisor.kepalaCabang');
            Log::info('Supervisor Load: ' . $user);

        } else if ($jabatan->id_jabatan == 4) {
            $user->load('pegawaiAdminKas.cabang', 'pegawaiAdminKas.wilayah', 'pegawaiAdminKas.supervisor');
        } else if ($jabatan->id_jabatan == 5) {
            $user->load('pegawaiAccountOfficer.cabang', 'pegawaiAccountOfficer.wilayah', 'pegawaiAccountOfficer.adminKas');
        }
    
        Log::info('User loaded with relations: ' . $user);
    
        $userDetails = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'jabatan' => $jabatan->nama_jabatan // Menampilkan nama jabatan
        ];
    
        if ($jabatan->id_jabatan == 2) {
            $pegawaiKepalaCabang = $user->pegawaiKepalaCabang;
            if ($pegawaiKepalaCabang) {
                $cabang = $pegawaiKepalaCabang->cabang;
                $direksi = $pegawaiKepalaCabang->direksi;
    
                Log::info('Kepala Cabang: ' . $pegawaiKepalaCabang);
                Log::info('Cabang: ' . $cabang);
                Log::info('Direksi: ' . $direksi);
    
                $userDetails['cabang'] = $cabang ? $cabang->nama_cabang : null;
                $userDetails['id_direksi'] = $direksi ? $direksi->nama : null;
            }
        } else if ($jabatan->id_jabatan == 3) {
            $pegawaiSupervisor = $user->pegawaiSupervisor;
            if ($pegawaiSupervisor) {
                $cabang = $pegawaiSupervisor->cabang;
                $wilayah = $pegawaiSupervisor->wilayah;
                $kepalaCabang = $pegawaiSupervisor->kepalaCabang;
    
                Log::info('Supervisor: ' . $pegawaiSupervisor);
                Log::info('Cabang: ' . $cabang);
                Log::info('Wilayah: ' . $wilayah);
                Log::info('Kepala Cabang: ' . $kepalaCabang);
    
                $userDetails['cabang'] = $cabang ? $cabang->nama_cabang : null;
                $userDetails['wilayah'] = $wilayah ? $wilayah->nama_wilayah : null;
                $userDetails['id_kepala_cabang'] = $kepalaCabang ? $kepalaCabang->nama_kepala_cabang : null;
            }
        } else if ($jabatan->id_jabatan == 4) {
            $pegawaiAdminKas = $user->pegawaiAdminKas;
            if ($pegawaiAdminKas) {
                $cabang = $pegawaiAdminKas->cabang;
                $wilayah = $pegawaiAdminKas->wilayah;
                $supervisor = $pegawaiAdminKas->supervisor;
    
                Log::info('Admin Kas: ' . $pegawaiAdminKas);
                Log::info('Cabang: ' . $cabang);
                Log::info('Wilayah: ' . $wilayah);
                Log::info('Supervisor: ' . $supervisor);
    
                $userDetails['cabang'] = $cabang ? $cabang->nama_cabang : null;
                $userDetails['wilayah'] = $wilayah ? $wilayah->nama_wilayah : null;
                $userDetails['id_supervisor'] = $supervisor ? $supervisor->nama_supervisor : null;
            }
        } else if ($jabatan->id_jabatan == 5) {
            $pegawaiAccountOfficer = $user->pegawaiAccountOfficer;
            if ($pegawaiAccountOfficer) {
                $cabang = $pegawaiAccountOfficer->cabang;
                $wilayah = $pegawaiAccountOfficer->wilayah;
                $adminKas = $pegawaiAccountOfficer->adminKas;
    
                Log::info('Account Officer: ' . $pegawaiAccountOfficer);
                Log::info('Cabang: ' . $cabang);
                Log::info('Wilayah: ' . $wilayah);
                Log::info('Admin Kas: ' . $adminKas);
    
                $userDetails['cabang'] = $cabang ? $cabang->nama_cabang : null;
                $userDetails['wilayah'] = $wilayah ? $wilayah->nama_wilayah : null;
                $userDetails['id_admin_kas'] = $adminKas ? $adminKas->nama_admin_kas : null;
            }
        }
    
        // Log informasi akhir sebelum respons
        Log::info('User details fetched successfully for user: ' . $user->name);
    
        return response()->json($userDetails);
    }
    

    public function register(Request $request)
    {
        // Validasi input dengan pesan dalam bahasa Indonesia
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'jabatan_id' => 'required|integer',
            'nip' => 'required|integer',
        ], [
            'required' => 'Kolom :attribute wajib diisi.',
            'email' => 'Format :attribute tidak valid.',
            'unique' => ':attribute sudah digunakan.',
            'min' => 'Minimal :attribute karakter.',
            'integer' => 'Kolom :attribute harus berupa angka.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Cek apakah NIP ada di tabel Nip
        $nipExists = Nip::where('nip', $request->nip)->exists();
        if (!$nipExists) {
            return response()->json(['message' => 'NIP tidak valid.'], 422);
        }
    
        // Cek apakah NIP sudah digunakan di tabel users
        $nipUsed = User::where('nip', $request->nip)->exists();
        if ($nipUsed) {
            return response()->json(['message' => 'NIP sudah digunakan.'], 422);
        }
    
        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'jabatan_id' => $request->jabatan_id,
            'nip' => $request->nip, // Simpan NIP
        ]);
    
        // Respon dengan user yang baru dibuat
        return response()->json(['message' => 'User berhasil terdaftar', 'user' => $user], 201);
    }
    

    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate a token (you may want to use a stronger hashing method)
            $token = md5(time() . '.' . md5($request->email));

            // Save the token to the user's api_token field
            $user->forceFill([
                'api_token' => $token,
            ])->save();

            // Return the token in the JSON response
            return response()->json([
                'user_id' => $user->id,
                'token' => $token,
                
            ]);
        }

        // If authentication fails
        return response()->json([
            'message' => 'The provided credentials do not match our records.',
        ], 401); // Unauthorized status code
    }

    public function logout(Request $request)
    {
        $request->user()->forceFill([
            'api_token' => null,
        ])->save();
        return response()->json([
            'message' => 'success'
        ]);
    }
    public function jabatan()
    {
        $jabatan = Jabatan::all();
        return response()->json($jabatan);
    }
    public function cabang()
    {
        $cabang = Cabang::all();
        return response()->json($cabang);
    }
    public function wilayah()
    {
        $wilayah = Wilayah::all();
        return response()->json($wilayah);
    }
    public function direksi()
    {
        $direksi = Direksi::all();
        return response()->json($direksi);
    }
    public function kepalacabang()
    {
        $kepalacabang = PegawaiKepalaCabang::all();
        return response()->json($kepalacabang);
    }
    public function supervisor()
    {
        $supervisor = PegawaiSupervisor::all();
        return response()->json($supervisor);
    }
    public function adminkas()
    {
        $adminkas = PegawaiAdminKas::all();
        return response()->json($adminkas);
    }
    public function accountofficer()
    {
        $accountofficer = PegawaiAccountOffice::all();
        return response()->json($accountofficer);
    }
    public function updateKepalaCabang(Request $request)
    {
        // Log the beginning of the request
        Log::info('updateCabang request received', $request->all());

        // Validate the request
        $validated = $request->validate([
            'id_cabang' => 'required|integer',
            'id_user' => 'required|integer',
            'id_direksi' => 'required|integer'
        ]);

        Log::info('Validated data', $validated);

        // Find the pegawai_kepala_cabang record for the user
        $pegawaiKepalaCabang = PegawaiKepalaCabang::where('id_user', $validated['id_user'])->first();

        if ($pegawaiKepalaCabang) {
            Log::info('PegawaiKepalaCabang found', ['id_user' => $validated['id_user']]);

            $pegawaiKepalaCabang->id_cabang = $validated['id_cabang'];
            $pegawaiKepalaCabang->id_direksi = $validated['id_direksi'];
            $pegawaiKepalaCabang->save();

            Log::info('PegawaiKepalaCabang updated', $pegawaiKepalaCabang->toArray());

            return response()->json(['message' => 'Update berhasil'], 200);
        } else {
            Log::warning('Pegawai tidak ditemukan', ['id_user' => $validated['id_user']]);
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }
    }
    public function updateSupervisor(Request $request)
    {
        // Log the beginning of the request
        Log::info('updateSupervisorrequest received', $request->all());

        // Validate the request
        $validated = $request->validate([
            'id_cabang' => 'required|integer',
            'id_user' => 'required|integer',
            'id_wilayah' => 'required|integer',
            'id_kepala_cabang' => 'required|integer'
        ]);

        Log::info('Validated data', $validated);

        // Find the pegawaiSupervisor record for the user
        $pegawaiSupervisor = PegawaiSupervisor::where('id_user', $validated['id_user'])->first();

        if ($pegawaiSupervisor) {
            Log::info('pegawaiSupervisor found', ['id_user' => $validated['id_user']]);

            $pegawaiSupervisor->id_cabang = $validated['id_cabang'];
            $pegawaiSupervisor->id_wilayah = $validated['id_wilayah'];
            $pegawaiSupervisor->id_kepala_cabang = $validated['id_kepala_cabang'];
            $pegawaiSupervisor->save();

            Log::info('pegawaiSupervisor updated', $pegawaiSupervisor->toArray());

            return response()->json(['message' => 'Update berhasil'], 200);
        } else {
            Log::warning('Pegawai tidak ditemukan', ['id_user' => $validated['id_user']]);
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }
    }

    public function updateAdminKas(Request $request)
    {
        // Log the beginning of the request
        Log::info('updateAdminKasrequest received', $request->all());

        // Validate the request
        $validated = $request->validate([
            'id_cabang' => 'required|integer',
            'id_user' => 'required|integer',
            'id_wilayah' => 'required|integer',
            'id_supervisor' => 'required|integer'
        ]);

        Log::info('Validated data', $validated);

        // Find the pegawaiAdminKas record for the user
        $pegawaiAdminKas = PegawaiAdminKas::where('id_user', $validated['id_user'])->first();

        if ($pegawaiAdminKas) {
            Log::info('pegawaiAdminKas found', ['id_user' => $validated['id_user']]);

            $pegawaiAdminKas->id_cabang = $validated['id_cabang'];
            $pegawaiAdminKas->id_wilayah = $validated['id_wilayah'];
            $pegawaiAdminKas->id_supervisor = $validated['id_supervisor'];
            $pegawaiAdminKas->save();

            Log::info('pegawaiAdminKas updated', $pegawaiAdminKas->toArray());

            return response()->json(['message' => 'Update berhasil'], 200);
        } else {
            Log::warning('Pegawai tidak ditemukan', ['id_user' => $validated['id_user']]);
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }
    }

    public function updateAccountOfficer(Request $request)
    {
        // Log the beginning of the request
        Log::info('updateAccountOfficer request received', $request->all());

        // Validate the request
        $validated = $request->validate([
            'id_cabang' => 'required|integer',
            'id_user' => 'required|integer',
            'id_wilayah' => 'required|integer',
            'id_admin_kas' => 'required|integer'
        ]);

        Log::info('Validated data', $validated);

        // Find the pegawaiAccountOfficer record for the user
        $pegawaiAccountOfficer = PegawaiAccountOffice::where('id_user', $validated['id_user'])->first();

        if ($pegawaiAccountOfficer) {
            Log::info('pegawaiAccountOfficer found', ['id_user' => $validated['id_user']]);

            $pegawaiAccountOfficer->id_cabang = $validated['id_cabang'];
            $pegawaiAccountOfficer->id_wilayah = $validated['id_wilayah'];
            $pegawaiAccountOfficer->id_admin_kas = $validated['id_admin_kas'];
            $pegawaiAccountOfficer->save();

            Log::info('pegawaiAccountOfficer updated', $pegawaiAccountOfficer->toArray());

            return response()->json(['message' => 'Update berhasil'], 200);
        } else {
            Log::warning('Pegawai tidak ditemukan', ['id_user' => $validated['id_user']]);
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }
    }
    // public function getNasabah(Request $request)
    // {
    //     Log::info('Request received for getCustomers', ['request' => $request->all()]);
    //     $user = Auth::user();
    //     Log::info('Authenticated user', ['user' => $user]);
    //     $jabatan = $user->jabatan->nama_jabatan;
    //     Log::info('Jabatan user', ['user' => $jabatan]);

    //     $perPage = 15; // Jumlah nasabah per halaman
    //     $query = Nasabah::with('cabang'); // Lakukan eager loading untuk tabel cabang

    //     switch ($jabatan) {
    //         case 'Direksi':
    //             // Tidak ada filter tambahan
    //             Log::info('Jabatan: direksi - fetching all nasabahs');
    //             break;

    //         case 'Kepala Cabang':
    //             $pegawaiKepalaCabang = PegawaiKepalaCabang::where('id_user', $user->id)->first();
    //             if ($pegawaiKepalaCabang) {
    //                 $idCabang = $pegawaiKepalaCabang->id_cabang;
    //                 Log::info('Jabatan: kepala_cabang - fetching nasabahs for cabang', ['id_cabang' => $idCabang]);
    //                 $query->where('id_cabang', $idCabang);
    //             } else {
    //                 return response()->json(['error' => 'Cabang not found for this Kepala Cabang'], 403);
    //             }
    //             break;

    //         case 'Supervisor':
    //             Log::info('Jabatan: supervisor - fetching nasabahs for cabang and wilayah', ['id_cabang' => $user->id_cabang, 'id_wilayah' => $user->id_wilayah]);
    //             $query->where('id_cabang', $user->id_cabang)
    //                 ->where('id_wilayah', $user->id_wilayah);
    //             break;

    //         case 'Admin Kas':
    //             Log::info('Jabatan: admin_kas - fetching nasabahs for admin_kas', ['id_admin_kas' => $user->id]);
    //             $query->where('id_admin_kas', $user->id);
    //             break;

    //         case 'Account Officer':
    //             Log::info('Jabatan: account_officer - fetching nasabahs for account_officer', ['id_account_officer' => $user->id]);
    //             $query->where('id_account_officer', $user->id);
    //             break;

    //         default:
    //             return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     if ($request->has('search')) {
    //         $search = $request->search;
    //         Log::info('Search parameter provided', ['search' => $search]);
    //         $query->where(function ($q) use ($search) {
    //             $q->where('nama', 'LIKE', '%' . $search . '%')
    //             ->orWhereHas('cabang', function($q) use ($search) {
    //                 $q->where('nama_cabang', 'LIKE', '%' . $search . '%');
    //             });
    //         });
    //     }

    //     $nasabahs = $query->paginate($perPage);
    //     Log::info('Customers fetched successfully', ['customers' => $nasabahs]);
    //     return response()->json($nasabahs);
    // }
    public function getNasabahSP()
    {
        $user = Auth::user();
        
        // Pastikan pengguna ditemukan
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Ambil PegawaiAccountOfficer berdasarkan id_user
        $pegawaiAccountOfficer = PegawaiAccountOffice::where('id_user', $user->id)->first();

        // Pastikan PegawaiAccountOfficer ditemukan
        if ($pegawaiAccountOfficer) {
            $idUser = $pegawaiAccountOfficer->id_account_officer;
            Log::info('Jabatan: account_officer - fetching nasabahs for account_officer', ['id_account_officer' => $idUser]);
            
            // Ambil data nasabah berdasarkan id_account_officer
            $nasabah = Nasabah::with('accountofficer')->where('id_account_officer', $idUser)->get();
            return response()->json($nasabah, 200);
        } else {
            return response()->json(['error' => 'Account Officer not found for this user'], 403);
        }
    }

    public function SuratPeringatan(Request $request)
    {
        try {
            // Log data yang diterima
            Log::info('Data yang diterima untuk SuratPeringatan: ' . json_encode($request->all()));

            $validated = $request->validate([
                'no' => 'required|integer',
                'tingkat' => 'required|integer',
                'tanggal' => 'required|date',
                'keterangan' => 'nullable|string',
                'bukti_gambar' => 'required|image|mimes:jpeg,png,jpg,gif', // Validate image file
                'scan_pdf' => 'required|mimes:pdf|max:2048', // Validate PDF file
                'idAccountOfficer' => 'required|integer'
            ]);

            // Log file details before saving
            if ($request->hasFile('bukti_gambar')) {
                Log::info('bukti_gambar: ' . $request->file('bukti_gambar')->getClientOriginalName() . ', size: ' . $request->file('bukti_gambar')->getSize() . ' bytes');
            }
            if ($request->hasFile('scan_pdf')) {
                Log::info('scan_pdf: ' . $request->file('scan_pdf')->getClientOriginalName() . ', size: ' . $request->file('scan_pdf')->getSize() . ' bytes');
            }

            // Ambil tingkat untuk digunakan dalam nama file
            $tingkat = $validated['tingkat'];
            $namaNasabah = $validated['no'];

            // Cek apakah sudah ada tingkat yang lebih rendah yang sudah terisi
            for ($i = 1; $i < $tingkat; $i++) {
                $existingSuratPeringatan = SuratPeringatan::where('no', $validated['no'])
                    ->where('tingkat', $i)
                    ->first();

                if (!$existingSuratPeringatan) {
                    Log::info("Tingkat $i belum diisi untuk Nasabah No: " . $validated['no']);
                    return response()->json(['error' => "Tingkat $i belum diisi untuk Nasabah ini. Harap isi tingkat yang lebih rendah terlebih dahulu."], 422);
                }
            }

            // Simpan gambar dan PDF
            $buktiGambar = $request->file('bukti_gambar');
            $scanPdf = $request->file('scan_pdf');

            if ($buktiGambar->isValid() && $scanPdf->isValid()) {
                $buktiGambarName = 'gambar_SP' . $tingkat . '_' . $namaNasabah . '.' . $buktiGambar->getClientOriginalExtension();
                $scanPdfName = 'pdf_SP' . $tingkat . '_' . $namaNasabah . '.' . $scanPdf->getClientOriginalExtension();

                // Cek apakah sudah ada entri dengan nama gambar atau PDF yang sama
                $existingSuratPeringatan = SuratPeringatan::where('bukti_gambar', 'like', '%/' . $buktiGambarName)
                    ->first();

                if ($existingSuratPeringatan) {
                    Log::info('Data sudah ada untuk gambar atau PDF: ' . $buktiGambarName . ', ' . $scanPdfName);
                    return response()->json(['error' => 'Data sudah ada untuk Nasabah SP ini.'], 422);
                }

                $buktiGambarPath = $buktiGambar->storeAs('private/surat_peringatan', $buktiGambarName);
                $scanPdfPath = $scanPdf->storeAs('private/surat_peringatan', $scanPdfName);

                Log::info('File gambar berhasil disimpan: ' . $buktiGambarName);
                Log::info('File PDF berhasil disimpan: ' . $scanPdfName);
            } else {
                Log::error('File gambar atau PDF tidak valid');
                throw new \Exception('File gambar atau PDF tidak valid');
            }

            // Buat record surat peringatan
            $suratPeringatan = SuratPeringatan::create([
                'no' => $validated['no'],
                'tingkat' => $validated['tingkat'],
                'tanggal' => $validated['tanggal'],
                'keterangan' => $validated['keterangan'],
                'bukti_gambar' => $buktiGambarPath,
                'scan_pdf' => $scanPdfPath,
                'id_account_officer' => $validated['idAccountOfficer'],
            ]);

            Log::info('Surat peringatan berhasil dibuat: ' . json_encode($suratPeringatan));

            return response()->json($suratPeringatan, 201);
        } catch (ValidationException $e) {
            // Log pesan validasi
            Log::error('Validasi gagal: ' . $e->getMessage());

            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log pesan error umum
            Log::error('Terjadi kesalahan saat menyimpan surat peringatan: ' . $e->getMessage());

            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan surat peringatan.'], 500);
        }
    }


    public function getNasabah(Request $request)
    {
        Log::info('Request received for getNasabah', ['request' => $request->all()]);
        $user = Auth::user();
        Log::info('Authenticated user', ['user' => $user]);
        $jabatan = $user->jabatan->nama_jabatan;
        Log::info('Jabatan user', ['jabatan' => $jabatan]);

        $perPage = 15; // Jumlah nasabah per halaman

        // Eager load semua relasi yang diperlukan
        $query = Nasabah::with([
            'cabang:id_cabang,nama_cabang',
            'wilayah:id_wilayah,nama_wilayah',
            'adminkas:id_admin_kas,nama_admin_kas',
            'accountofficer:id_account_officer,nama_account_officer',
            'suratPeringatan' => function ($query) {
                $query->orderBy('tingkat', 'desc'); // Urutkan surat peringatan berdasarkan tingkat dari yang terbesar
            }
        ]);

        switch ($jabatan) {
            case 'Direksi':
                Log::info('Jabatan: direksi - fetching all nasabahs');
                break;

            case 'Kepala Cabang':
                $pegawaiKepalaCabang = PegawaiKepalaCabang::where('id_user', $user->id)->first();
                if ($pegawaiKepalaCabang) {
                    $idCabang = $pegawaiKepalaCabang->id_cabang;
                    Log::info('Jabatan: kepala_cabang - fetching nasabahs for cabang', ['id_cabang' => $idCabang]);
                    $query->where('id_cabang', $idCabang);
                } else {
                    Log::error('Cabang not found for Kepala Cabang', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Cabang not found for this Kepala Cabang'], 403);
                }
                break;

            case 'Supervisor':
                $pegawaiSupervisor = PegawaiSupervisor::where('id_user', $user->id)->first();
                if ($pegawaiSupervisor) {
                    $idCabang = $pegawaiSupervisor->id_cabang;
                    $idWilayah = $pegawaiSupervisor->id_wilayah;
                    Log::info('Jabatan: supervisor - fetching nasabahs for cabang and wilayah', ['id_cabang' => $idCabang, 'id_wilayah' => $idWilayah]);
                    $query->where('id_cabang', $idCabang)->where('id_wilayah', $idWilayah);
                } else {
                    Log::error('Supervisor not found for Supervisor', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Supervisor not found for this Supervisor'], 403);
                }
                break;

            case 'Admin Kas':
                $pegawaiAdminKas = PegawaiAdminKas::where('id_user', $user->id)->first();
                if ($pegawaiAdminKas) {
                    $idUser = $pegawaiAdminKas->id_admin_kas;
                    Log::info('Jabatan: admin_kas - fetching nasabahs for admin_kas', ['id_admin_kas' => $idUser]);
                    $query->where('id_admin_kas', $idUser);
                } else {
                    Log::error('Admin Kas not found for Admin Kas', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Admin Kas not found for this Admin Kas'], 403);
                }
                break;

            case 'Account Officer':
                $pegawaiAccountOfficer = PegawaiAccountOffice::where('id_user', $user->id)->first();
                if ($pegawaiAccountOfficer) {
                    $idUser = $pegawaiAccountOfficer->id_account_officer;
                    Log::info('Jabatan: account_officer - fetching nasabahs for account_officer', ['id_account_officer' => $idUser]);
                    $query->where('id_account_officer', $idUser);
                } else {
                    Log::error('Account Officer not found for Account Officer', ['user_id' => $user->id]);
                    return response()->json(['error' => 'Account Officer not found for this Account Officer'], 403);
                }
                break;

            default:
                Log::warning('Unauthorized access attempt', ['user_id' => $user->id, 'jabatan' => $jabatan]);
                return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->has('search')) {
            $search = $request->search;
            Log::info('Search parameter provided', ['search' => $search]);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('cabang', function($q) use ($search) {
                        $q->where('nama_cabang', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        Log::info('Executing query to fetch nasabahs');
        $nasabahs = $query->paginate($perPage);

        Log::info('Transforming nasabah data to include all Surat Peringatan');
        $nasabahs->getCollection()->transform(function($nasabah) {
            $allSuratPeringatan = $nasabah->suratPeringatan->map(function($suratPeringatan) {
                return [
                    'no' => $suratPeringatan->no,
                    'tingkat' => $suratPeringatan->tingkat,
                    'tanggal' => $suratPeringatan->tanggal,
                    'keterangan' => $suratPeringatan->keterangan,
                    'bukti_gambar' => $suratPeringatan->bukti_gambar,
                    'scan_pdf' => $suratPeringatan->scan_pdf,
                    'id_account_officer' => $suratPeringatan->id_account_officer,
                ];
            });

            return [
                'no' => $nasabah->no,
                'nama' => $nasabah->nama,
                'nama_cabang' => $nasabah->cabang->nama_cabang,
                'surat_peringatan' => $allSuratPeringatan->toArray(), // Convert collection to array
            ];
        });

        Log::info('Customers fetched successfully', ['customers' => $nasabahs->toArray()]);
        return response()->json($nasabahs->toArray());
    }




    public function checkConnection()
    {
        return response()->json(['message' => 'Server connection is OK!'], 200);
    }
    public function show($id)
    {
        $nasabah = Nasabah::find($id);

        if ($nasabah) {
            return response()->json($nasabah);
        } else {
            return response()->json(['error' => 'Nasabah not found'], 404);
        }
    }

    public function getSuratPeringatan(Request $request)
    {
        $nasabahNo = $request->query('nasabah_no');

        Log::info("Fetching Surat Peringatan for Nasabah No: $nasabahNo");

        try {
            $suratPeringatan = SuratPeringatan::where('no', $nasabahNo)
                ->orderBy('tingkat', 'desc') // Mengurutkan berdasarkan tingkat dari yang terbesar
                ->first();

            if ($suratPeringatan) {
                $tingkat = $suratPeringatan->tingkat;
                Log::info("Highest Level Surat Peringatan found for Nasabah No: $nasabahNo, Tingkat: $tingkat");

                return response()->json($suratPeringatan, 200);
            } else {
                Log::info("Surat Peringatan not found for Nasabah No: $nasabahNo");

                return response()->json(['message' => 'Surat Peringatan not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error("Error fetching Surat Peringatan: ", ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Error fetching Surat Peringatan'], 500);
        }
    }

    public function serveImage($filename)
    {
        $path = storage_path('app/private/surat_peringatan/' . $filename);
        if (file_exists($path)) {
            Log::info("Serving image from path: " . $path);
            return response()->file($path);
        } else {
            Log::error("Image not found at path: " . $path);
            return response()->json(['error' => 'Image not found'], 404);
        }
    }
    
    public function servePdf($filename)
    {
        $path = storage_path('app/private/surat_peringatan/' . $filename);
        if (file_exists($path)) {
            Log::info("Serving PDF from path: " . $path);
            return response()->file($path);
        } else {
            Log::error("PDF not found at path: " . $path);
            return response()->json(['error' => 'PDF not found'], 404);
        }
    }
    


}