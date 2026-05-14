<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>APPDAL - Dinas Sosial Kabupaten Kapuas</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .gradient-text {
            background: linear-gradient(90deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <nav class="fixed w-full z-50 transition-all duration-300 border-b border-transparent"
        :class="scrolled ? 'glass shadow-md py-3 border-slate-200' : 'bg-transparent py-5'">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2 rounded-lg shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <span class="font-bold text-xl tracking-tight transition-colors duration-300"
                    :class="scrolled ? 'text-blue-900' : 'text-white'">APPDAL</span>
            </div>

            <div class="hidden lg:flex space-x-8 font-medium transition-colors duration-300"
                :class="scrolled ? 'text-slate-600' : 'text-slate-200'">
                <a href="#home" class="hover:text-blue-500 transition">Beranda</a>
                <a href="#profile" class="hover:text-blue-500 transition">Profil</a>
                <a href="#check" class="hover:text-blue-500 transition">Cek Bantuan</a>
            </div>

            <div>
                <a href="{{ url('/admin/login') }}"
                    class="px-6 py-2.5 rounded-full font-semibold transition-all duration-300 shadow-lg border-2"
                    :class="scrolled ? 'border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white' :
                        'border-white text-white hover:bg-white hover:text-blue-900'">
                    Login Admin
                </a>
            </div>
        </div>
    </nav>

    <section id="home" class="relative min-h-screen flex items-center pt-20 overflow-hidden bg-slate-900">
        <div class="container mx-auto px-6 relative z-10 text-center max-w-4xl">
            <span
                class="inline-block bg-blue-500/20 border border-blue-400/30 text-blue-200 px-5 py-2 rounded-full text-sm font-semibold uppercase tracking-widest backdrop-blur-sm mb-6">
                Dinas Sosial Kabupaten Kapuas
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold text-white leading-tight drop-shadow-lg">
                Transparansi <br><span class="gradient-text">Bantuan Sosial</span><br> Lebih Mudah.
            </h1>
            <p
                class="text-slate-300 mt-8 text-lg md:text-xl leading-relaxed font-light drop-shadow-md max-w-2xl mx-auto">
                Aplikasi Pengelolaan Penerima Bantuan Berbasis Web di Dinas Sosial Kabupaten Kapuas</p>
            <div
                class="mt-12 flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#check"
                    class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-500 hover:shadow-blue-500/50 hover:shadow-2xl hover:-translate-y-1 transition duration-300 w-full sm:w-auto">
                    Cek Status Anda
                </a>
                <a href="#alur"
                    class="bg-white/10 text-white border border-white/20 backdrop-blur-md px-8 py-4 rounded-xl font-bold hover:bg-white/20 transition duration-300 w-full sm:w-auto">
                    Cara Daftar DTKS
                </a>
            </div>
        </div>
    </section>

    <section id="statistik"
        class="py-16 bg-white relative -mt-10 z-20 rounded-t-[3rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-slate-100">
                <div class="p-4">
                    <h3 class="text-4xl font-extrabold text-blue-600 mb-2">12.5K+</h3>
                    <p class="text-slate-500 font-medium">Keluarga Penerima Manfaat</p>
                </div>
                <div class="p-4">
                    <h3 class="text-4xl font-extrabold text-blue-600 mb-2">214</h3>
                    <p class="text-slate-500 font-medium">Desa Terjangkau</p>
                </div>
                <div class="p-4">
                    <h3 class="text-4xl font-extrabold text-blue-600 mb-2">17</h3>
                    <p class="text-slate-500 font-medium">Kecamatan Terjangkau</p>
                </div>
                <div class="p-4">
                    <h3 class="text-4xl font-extrabold text-blue-600 mb-2">4</h3>
                    <p class="text-slate-500 font-medium">Program Bantuan Aktif</p>
                </div>
            </div>
        </div>
    </section>

    <section id="profile" class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-blue-600 font-semibold uppercase tracking-wider text-sm">Tentang Kami</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 mb-4 text-slate-800">Profil Dinas Sosial</h2>
                <div class="h-1.5 w-20 bg-blue-600 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div
                    class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition duration-300">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04L3 9c0 5.682 2.366 10.842 6.182 14.545l.818.794.818-.794A11.955 11.955 0 0121 9l-.618-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-800 text-center md:text-left">Visi & Misi Terarah</h3>
                    <p class="text-slate-500 leading-relaxed text-center md:text-left">Mewujudkan kesejahteraan sosial
                        bagi seluruh lapisan
                        masyarakat di Kabupaten Kapuas melalui pelayanan yang profesional, transparan, dan berkeadilan.
                    </p>
                </div>

                <div
                    class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition duration-300">
                    <div
                        class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-800 text-center md:text-left">Integrasi Program</h3>
                    <p class="text-slate-500 leading-relaxed text-center md:text-left">Mengelola seluruh program bantuan
                        pemerintah pusat &
                        daerah (PKH, BPNT, PBI-JK, ATENSI) dalam satu pintu aplikasi cerdas.</p>
                </div>

                <div
                    class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-2 transition duration-300">
                    <div
                        class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 mx-auto md:mx-0">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-800 text-center md:text-left">Validasi Real-Time</h3>
                    <p class="text-slate-500 leading-relaxed text-center md:text-left">Dilengkapi sistem pemetaan
                        kordinat dan survei foto rumah
                        secara langsung untuk memastikan validitas data kemiskinan (Desil) yang akurat.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="alur" class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-5xl">
            <div class="text-center mb-16">
                <span class="text-blue-600 font-semibold uppercase tracking-wider text-sm">Informasi Publik</span>
                <h2 class="text-3xl md:text-4xl font-bold mt-2 text-slate-800">Alur Pendaftaran DTKS</h2>
            </div>

            <div class="space-y-8 max-w-3xl mx-auto">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-5">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                            1</div>
                    </div>
                    <div class="pt-1">
                        <h4 class="text-xl font-bold text-slate-800">Lapor ke Desa/Kelurahan</h4>
                        <p class="text-slate-500 mt-2">Membawa KTP dan KK asli ke kantor Desa atau Kelurahan
                            setempat untuk didaftarkan dalam musyawarah desa.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-5">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                            2</div>
                    </div>
                    <div class="pt-1">
                        <h4 class="text-xl font-bold text-slate-800">Survei Lapangan</h4>
                        <p class="text-slate-500 mt-2">Tim APPDAL (TKSK/Pendamping) akan datang ke rumah untuk
                            melakukan wawancara, mengambil titik koordinat, dan foto kondisi rumah.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-5">
                        <div
                            class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                            3</div>
                    </div>
                    <div class="pt-1">
                        <h4 class="text-xl font-bold text-slate-800">Verifikasi Dinas & Kemensos</h4>
                        <p class="text-slate-500 mt-2">Data diproses di sistem untuk penentuan skor desil
                            kemiskinan dan sinkronisasi dengan Dukcapil & Kementerian Sosial.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="check" class="py-24 bg-slate-900 relative overflow-hidden">
        <div class="container mx-auto px-6 relative z-10" x-data="{
            kk: '',
            loading: false,
            result: null,
            errorMsg: '',
            async findData() {
                if (!this.kk) return;
                this.loading = true;
                this.result = null;
                this.errorMsg = '';
        
                try {
                    const response = await fetch('{{ url('/cek-bantuan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ no_kk: this.kk })
                    });
        
                    const data = await response.json();
        
                    if (data.status === 'found') {
                        this.result = data;
                    } else {
                        this.errorMsg = 'Data dengan No. KK tersebut tidak ditemukan atau belum terdaftar di sistem kami.';
                    }
                } catch (e) {
                    this.errorMsg = 'Terjadi kesalahan saat memeriksa data. Silakan coba lagi nanti.';
                } finally {
                    this.loading = false;
                }
            }
        }">
            <div
                class="bg-white/10 backdrop-blur-xl border border-white/20 p-8 md:p-12 rounded-[2.5rem] shadow-2xl max-w-4xl mx-auto flex flex-col items-center">

                <div class="text-center mb-10 w-full">
                    <span
                        class="bg-blue-500/30 text-blue-200 px-4 py-1.5 rounded-full text-sm font-semibold tracking-wider inline-block">PORTAL
                        TRANSPARANSI</span>
                    <h2 class="text-3xl font-bold text-white mt-4">Cek Status Bantuan Anda</h2>
                    <p class="text-blue-100 mt-2 font-light">Masukkan No. Kartu Keluarga (KK) 16 Digit Anda.</p>
                </div>

                <div class="flex flex-col md:flex-row gap-4 w-full justify-center max-w-2xl">
                    <input type="text" x-model="kk" @keydown.enter="findData()"
                        placeholder="Contoh: 6203xxxxxxxxxxxx"
                        class="flex-1 bg-white/90 focus:bg-white border-2 border-transparent focus:border-blue-400 p-5 rounded-2xl outline-none transition text-lg font-medium text-slate-800 shadow-inner text-center md:text-left">
                    <button @click="findData()" :disabled="loading"
                        class="bg-blue-500 text-white px-10 py-5 rounded-2xl font-bold hover:bg-blue-400 transition flex items-center justify-center disabled:opacity-70 shadow-[0_0_20px_rgba(59,130,246,0.5)]">
                        <span x-show="!loading">Lacak Status</span>
                        <svg x-show="loading" x-cloak class="animate-spin h-6 w-6 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>

                <div x-show="errorMsg" x-transition x-cloak
                    class="mt-6 w-full max-w-2xl bg-red-500/20 border border-red-500/50 text-white p-4 rounded-xl flex items-center gap-3 backdrop-blur-md justify-center">
                    <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="errorMsg" class="font-light text-center"></span>
                </div>

                <div x-show="result" x-transition x-cloak class="mt-12 bg-white rounded-3xl p-8 shadow-xl w-full">
                    <div
                        class="flex flex-col md:flex-row items-center md:items-start justify-between mb-8 pb-8 border-b border-slate-100 gap-6">
                        <div class="text-center md:text-left w-full md:w-auto">
                            <p class="text-sm text-slate-400 uppercase tracking-widest font-bold mb-1">Hasil Pencarian
                            </p>
                            <h4 class="text-2xl font-extrabold text-slate-800" x-text="'Keluarga ' + result?.name">
                            </h4>

                            <div x-show="result?.rekomendasi && result?.rekomendasi.length > 0"
                                class="mt-4 flex flex-wrap gap-2 justify-center md:justify-start items-center">
                                <span class="text-sm font-semibold text-slate-500 mr-1">Rekomendasi Program:</span>
                                <template x-for="rek in result?.rekomendasi">
                                    <span
                                        class="bg-blue-50 text-blue-700 border border-blue-200 px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                                        x-text="rek"></span>
                                </template>
                            </div>
                        </div>
                        <div
                            class="text-center md:text-right bg-slate-50 p-4 rounded-xl border border-slate-100 w-full md:w-auto min-w-[200px]">
                            <span class="text-xs font-bold text-slate-400 uppercase block mb-1">Status Kelayakan</span>
                            <span
                                class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-bold inline-block mb-2"
                                x-text="result?.status_text"></span>
                        </div>
                    </div>

                    <div class="relative w-full max-w-3xl mx-auto">
                        <div class="absolute left-6 md:left-1/2 top-0 bottom-0 w-0.5 bg-slate-100 md:-ml-px"></div>

                        <div class="space-y-8">
                            <div class="relative flex items-center justify-between"
                                :class="result?.step >= 1 ? 'opacity-100' : 'opacity-40 grayscale'">
                                <div class="hidden md:block w-5/12 pr-8 text-right">
                                    <h5 class="font-bold text-slate-800 text-lg">Input Data Masuk</h5>
                                    <p class="text-slate-500 text-sm mt-1">Sistem menerima usulan KK.</p>
                                </div>
                                <div class="absolute left-0 md:left-1/2 flex items-center justify-center w-12 h-12 rounded-full shadow-lg md:-ml-6 z-10 border-4 border-white transition-colors duration-500"
                                    :class="result?.step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-400'">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-16 md:ml-0 md:w-5/12 md:pl-8 text-left">
                                    <div class="md:hidden">
                                        <h5 class="font-bold text-slate-800 text-lg">Input Data Masuk</h5>
                                        <p class="text-slate-500 text-sm mt-1">Sistem menerima usulan KK.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex items-center justify-between"
                                :class="result?.step >= 2 ? 'opacity-100' : 'opacity-40 grayscale'">
                                <div class="hidden md:block w-5/12 pr-8 text-right"></div>
                                <div class="absolute left-0 md:left-1/2 flex items-center justify-center w-12 h-12 rounded-full shadow-lg md:-ml-6 z-10 border-4 border-white transition-colors duration-500"
                                    :class="result?.step >= 2 ? (result?.step === 2 ? 'bg-amber-500 text-white animate-pulse' :
                                        'bg-blue-600 text-white') : 'bg-slate-200 text-slate-400'">
                                    <div x-show="result?.step === 2" class="w-3 h-3 bg-white rounded-full"></div>
                                    <svg x-show="result?.step > 2" class="w-5 h-5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-16 md:ml-0 md:w-5/12 md:pl-8 text-left">
                                    <h5 class="font-bold text-slate-800 text-lg">Verifikasi Lapangan</h5>
                                    <p class="text-slate-500 text-sm mt-1">Survei koordinat & foto rumah selesai.</p>
                                </div>
                            </div>

                            <div class="relative flex items-center justify-between"
                                :class="result?.step >= 3 ? 'opacity-100' : 'opacity-40 grayscale'">
                                <div class="hidden md:block w-5/12 pr-8 text-right">
                                    <h5 class="font-bold text-slate-800 text-lg">Penilaian Kelayakan</h5>
                                    <p class="text-slate-500 text-sm mt-1">Scoring komponen & desil.</p>
                                </div>
                                <div class="absolute left-0 md:left-1/2 flex items-center justify-center w-12 h-12 rounded-full shadow-lg md:-ml-6 z-10 border-4 border-white transition-colors duration-500"
                                    :class="result?.step >= 3 ? (result?.step === 3 ? 'bg-amber-500 text-white animate-pulse' :
                                        'bg-blue-600 text-white') : 'bg-slate-200 text-slate-400'">
                                    <div x-show="result?.step === 3" class="w-3 h-3 bg-white rounded-full"></div>
                                    <svg x-show="result?.step > 3" class="w-5 h-5" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-16 md:ml-0 md:w-5/12 md:pl-8 text-left">
                                    <div class="md:hidden">
                                        <h5 class="font-bold text-slate-800 text-lg">Penilaian Kelayakan</h5>
                                        <p class="text-slate-500 text-sm mt-1">Scoring komponen & desil.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative flex items-center justify-between"
                                :class="result?.step >= 4 ? 'opacity-100' : 'opacity-40 grayscale'">
                                <div class="hidden md:block w-5/12 pr-8 text-right"></div>
                                <div class="absolute left-0 md:left-1/2 flex items-center justify-center w-12 h-12 rounded-full shadow-lg md:-ml-6 z-10 border-4 border-white transition-colors duration-500"
                                    :class="result?.step >= 4 ? 'bg-green-500 text-white' : 'bg-slate-200 text-slate-400'">
                                    <svg x-show="result?.step >= 4" class="w-6 h-6" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-16 md:ml-0 md:w-5/12 md:pl-8 text-left">
                                    <h5 class="font-bold text-slate-800 text-lg">Terdaftar DTKS / Penerima</h5>
                                    <p class="text-slate-500 text-sm mt-1">Menunggu jadwal penyaluran bantuan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact" class="bg-slate-900 text-slate-300 pt-20 pb-10">
        <div
            class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 border-b border-slate-800 pb-16">

            <div>
                <div class="flex items-center space-x-3 text-white mb-6">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <span class="font-bold text-2xl tracking-tight">APPDAL KAPUAS</span>
                </div>
                <p class="text-sm leading-relaxed text-slate-400 pr-4">Sistem informasi manajemen bantuan sosial
                    terintegrasi Dinas Sosial Kabupaten Kapuas. Dibangun untuk transparansi dan percepatan penanganan
                    sosial.</p>
            </div>

            <div>
                <h6 class="text-white font-bold mb-6 uppercase text-sm tracking-wider">Tautan Cepat</h6>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li><a href="#home" class="hover:text-blue-500 transition duration-300">Beranda</a></li>
                    <li><a href="#profile" class="hover:text-blue-500 transition duration-300">Profil Instansi</a>
                    </li>
                    <li><a href="#alur" class="hover:text-blue-500 transition duration-300">Alur Pendaftaran</a>
                    </li>
                    <li><a href="#check" class="hover:text-blue-500 transition duration-300">Cek Status Bantuan</a>
                    </li>
                    <li><a href="{{ url('/admin/login') }}" class="hover:text-blue-500 transition duration-300">Portal
                            Admin</a></li>
                </ul>
            </div>

            <div>
                <h6 class="text-white font-bold mb-6 uppercase text-sm tracking-wider">Kontak Instansi</h6>
                <ul class="space-y-4 text-sm text-slate-400">
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Jl. Patih Rumbih No. 11, Kuala Kapuas 73514, Kalimantan Tengah</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                            </path>
                        </svg>
                        <span>Telp: (0513) 21088 <br> Fax: (0513) 21088</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span>dinsos@kapuaskab.go.id</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                            </path>
                        </svg>
                        <span>dinsos.kapuaskab.go.id</span>
                    </li>
                </ul>
            </div>

            <div>
                <h6 class="text-white font-bold mb-6 uppercase text-sm tracking-wider">Lokasi Kami</h6>
                <div
                    class="rounded-xl overflow-hidden grayscale hover:grayscale-0 transition duration-500 shadow-lg h-48 border border-slate-700">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15933.264669862892!2d114.3739798!3d-3.0097653!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2de46eee0db53909%3A0xe5a363717dfc7db0!2sDinas%20Sosial%20Kabupaten%20Kapuas!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6 mt-8 text-center text-slate-500 text-sm">
            &copy; {{ date('Y') }} Dinas Sosial Kabupaten Kapuas. Hak Cipta Dilindungi.
        </div>
    </footer>

</body>

</html>
