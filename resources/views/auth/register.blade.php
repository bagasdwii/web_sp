<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Jabatan -->
        <div class="mt-4">
            <x-input-label for="jabatan_id" :value="__('Jabatan')" />
            <select id="jabatan_id" name="jabatan_id" class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                @foreach(\App\Models\Jabatan::all() as $jabatan)
                    <option value="{{ $jabatan->id_jabatan }}">{{ $jabatan->nama_jabatan }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('jabatan_id')" class="mt-2" />
        </div>

        <!-- Form untuk Pemilihan Cabang -->
        <div id="form-cabang" class="mt-4" style="display: none;">
            <x-input-label for="id_cabang" :value="__('Cabang')" />
            <select id="id_cabang" name="id_cabang" class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                @foreach($cabangs as $cabang)
                    <option value="{{ $cabang->id_cabang }}">{{ $cabang->nama_cabang }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('id_cabang')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#jabatan_id').change(function() {
                var jabatanId = $(this).val();
                // Sembunyikan form cabang dan direksi terlebih dahulu
                $('#form-cabang').hide();
                $('#form-direksi').hide();
                // Tampilkan form cabang dan direksi jika jabatan yang dipilih memerlukan pemilihan cabang dan direksi
                if (jabatanId == 2 || jabatanId == 3 || jabatanId == 4 || jabatanId == 5) {
                    $('#form-cabang').show();
                    $('#form-direksi').show();
                }
            });

            // Periksa jabatan saat halaman dimuat ulang
            var currentJabatanId = $('#jabatan_id').val();
            if (currentJabatanId == 2 || currentJabatanId == 3 || currentJabatanId == 4 || currentJabatanId == 5) {
                $('#form-cabang').show();
                $('#form-direksi').show();
            }
        });
    </script>
</x-guest-layout>
