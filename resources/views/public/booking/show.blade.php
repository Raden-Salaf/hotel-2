<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan {{ $room->name }} — Paijo's Hotel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center gap-4">
            <a href="{{ route('public.booking.index') }}"
                class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200
                  text-gray-500 hover:bg-gray-50 transition">
                <i class="ti ti-arrow-left"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: #16a34a;">
                    <i class="ti ti-building text-white text-lg"></i>
                </div>
                <span class="font-bold text-gray-800">Paijo's Hotel</span>
            </div>
        </div>
    </nav>

    {{-- ============================================
     WRAPPER UTAMA — parent Alpine scope
     Semua kalkulasi harga ada di sini
     Child item FnB komunikasi via $dispatch event
============================================ --}}
    <div class="max-w-6xl mx-auto px-4 py-8" x-data="{
        checkIn: '{{ now()->format('Y-m-d') }}',
        checkOut: '{{ now()->addDay()->format('Y-m-d') }}',
        pricePerNight: {{ $room->price_per_night }},
    
        {{-- Object menyimpan semua FnB yang dipilih --}}
        {{-- format: { id: { price, name, qty } } --}}
        fnbItems: {},
    
        get nights() {
            if (!this.checkIn || !this.checkOut) return 0
            const diff = Math.floor(
                (new Date(this.checkOut) - new Date(this.checkIn)) / (1000 * 60 * 60 * 24)
            )
            return diff > 0 ? diff : 0
        },
    
        get roomTotal() {
            return this.nights * this.pricePerNight
        },
    
        get fnbTotal() {
            let total = 0
            Object.values(this.fnbItems).forEach(item => {
                total += item.price * item.qty
            })
            return total
        },
    
        get grandTotal() {
            return this.roomTotal + this.fnbTotal
        },
    
        formatRupiah(num) {
            return 'Rp ' + Math.round(num).toLocaleString('id-ID')
        },
    
        {{-- Dipanggil saat menerima event fnb-update dari child --}}
        setFnb(id, price, name, qty) {
            if (qty <= 0) {
                delete this.fnbItems[id]
                this.fnbItems = { ...this.fnbItems }
            } else {
                this.fnbItems = {
                    ...this.fnbItems,
                    [id]: { price: price, name: name, qty: qty }
                }
            }
        }
    }" {{-- Tangkap event dari semua child item --}}
        @fnb-update.window="setFnb(
         $event.detail.id,
         $event.detail.price,
         $event.detail.name,
         $event.detail.qty
     )">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ==========================================
             KOLOM KIRI — Info Kamar + Form
        ========================================== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Foto kamar --}}
                <div class="rounded-2xl overflow-hidden relative" style="padding-top: 50%;">
                    <div class="absolute inset-0">
                        @if ($room->image)
                            <img src="{{ Storage::url($room->image) }}" alt="{{ $room->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center"
                                style="background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
                                <i class="ti ti-building-estate text-8xl" style="color:#bbf7d0;"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info kamar --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full inline-block mb-2"
                                style="background:#f0fdf4; color:#16a34a;">
                                {{ $room->category->name ?? '' }}
                            </span>
                            <h1 class="text-2xl font-bold text-gray-800">{{ $room->name }}</h1>
                            <p class="text-gray-400 text-sm mt-1">
                                No. {{ $room->room_number }} · Lantai {{ $room->floor }} ·
                                Maks. {{ $room->capacity }} tamu
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                            <p class="text-2xl font-bold" style="color:#16a34a;">
                                Rp {{ number_format($room->price_per_night, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400">per malam</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        {{ $room->description }}
                    </p>
                    @if ($room->facilities)
                        <div class="flex flex-wrap gap-2">
                            @foreach ($room->facilities as $f)
                                <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs text-gray-600"
                                    style="background:#f0fdf4;">
                                    <i class="ti ti-check text-green-500 text-xs"></i>
                                    {{ $f }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Form booking --}}
                <form action="{{ route('public.booking.store', $room) }}" method="POST" id="booking-form">
                    @csrf

                    {{-- Tanggal menginap --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-5">
                        <h2 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <i class="ti ti-calendar-check" style="color:#16a34a;"></i>
                            Tanggal Menginap
                        </h2>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Check-in <span class="text-red-400">*</span>
                                </label>
                                <input type="date" name="check_in" x-model="checkIn"
                                    min="{{ now()->format('Y-m-d') }}" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                          bg-gray-50 focus:outline-none focus:border-green-500
                                          focus:ring-2 focus:ring-green-100
                                          @error('check_in') border-red-400 @enderror">
                                @error('check_in')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Check-out <span class="text-red-400">*</span>
                                </label>
                                <input type="date" name="check_out" x-model="checkOut" :min="checkIn"
                                    required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                          bg-gray-50 focus:outline-none focus:border-green-500
                                          focus:ring-2 focus:ring-green-100
                                          @error('check_out') border-red-400 @enderror">
                                @error('check_out')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Info malam real-time --}}
                        <div class="px-4 py-3 rounded-xl text-sm flex items-center gap-2" style="background:#f0fdf4;"
                            x-show="nights > 0" x-transition>
                            <i class="ti ti-moon text-green-600"></i>
                            <span class="text-green-700 font-medium">
                                <span class="font-bold" x-text="nights"></span> malam ·
                                Total kamar:
                                <span class="font-bold" x-text="formatRupiah(roomTotal)"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Data tamu --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-5">
                        <h2 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <i class="ti ti-user" style="color:#16a34a;"></i>
                            Data Tamu
                        </h2>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Nama Lengkap <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                                        placeholder="Nama sesuai KTP" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                              bg-gray-50 focus:outline-none focus:border-green-500
                                              focus:ring-2 focus:ring-green-100
                                              @error('guest_name') border-red-400 @enderror">
                                    @error('guest_name')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        No. KTP / Passport
                                    </label>
                                    <input type="text" name="guest_id_card" value="{{ old('guest_id_card') }}"
                                        placeholder="Opsional"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                              bg-gray-50 focus:outline-none focus:border-green-500
                                              focus:ring-2 focus:ring-green-100">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Email <span class="text-red-400">*</span>
                                    </label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email') }}"
                                        placeholder="email@contoh.com" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                              bg-gray-50 focus:outline-none focus:border-green-500
                                              focus:ring-2 focus:ring-green-100
                                              @error('guest_email') border-red-400 @enderror">
                                    @error('guest_email')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        No. HP <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="guest_phone" value="{{ old('guest_phone') }}"
                                        placeholder="08xxxxxxxxxx" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                              bg-gray-50 focus:outline-none focus:border-green-500
                                              focus:ring-2 focus:ring-green-100
                                              @error('guest_phone') border-red-400 @enderror">
                                    @error('guest_phone')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                        Jumlah Tamu <span class="text-red-400">*</span>
                                    </label>
                                    <select name="num_guests"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                               bg-gray-50 focus:outline-none focus:border-green-500
                                               focus:ring-2 focus:ring-green-100">
                                        @for ($i = 1; $i <= $room->capacity; $i++)
                                            <option value="{{ $i }}">{{ $i }} Tamu</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                    Permintaan Khusus
                                </label>
                                <textarea name="special_requests" rows="3" placeholder="Contoh: extra bed, lantai tinggi, dll..."
                                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl
                                             bg-gray-50 focus:outline-none focus:border-green-500
                                             focus:ring-2 focus:ring-green-100 resize-none">{{ old('special_requests') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ==========================================
                     PILIH MENU FNB
                     Setiap item punya x-data qty sendiri
                     Kirim event ke parent via $dispatch
                ========================================== --}}
                    @if ($fnbItems->count() > 0)
                        <div class="bg-white rounded-2xl border border-gray-100 p-6">
                            <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                                <i class="ti ti-tools-kitchen-2" style="color:#16a34a;"></i>
                                Tambah Pesanan F&B
                            </h2>
                            <p class="text-xs text-gray-400 mb-5">
                                Opsional — bisa juga dipesan setelah check-in
                            </p>

                            @foreach ($fnbItems as $categoryId => $items)
                                @php $category = $fnbCategories[$categoryId] ?? null @endphp
                                @if ($category)
                                    <div class="mb-6">
                                        {{-- Header kategori --}}
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-lg">{{ $category->icon }}</span>
                                            <h3 class="text-sm font-bold text-gray-700">{{ $category->name }}</h3>
                                            <div class="flex-1 h-px bg-gray-100"></div>
                                        </div>

                                        <div class="space-y-2">
                                            @foreach ($items as $fnbItem)
                                                {{-- ==========================================
                                     ITEM FNB
                                     x-data lokal hanya untuk qty item ini
                                     Saat qty berubah → dispatch ke window
                                     Parent tangkap via @fnb-update.window
                                ========================================== --}}
                                                <div class="flex items-center gap-3 p-3 rounded-xl border transition"
                                                    x-data="{ qty: 0 }"
                                                    :class="qty > 0 ?
                                                        'border-green-300 bg-green-50' :
                                                        'border-gray-100 hover:border-gray-200'">

                                                    {{-- Foto menu --}}
                                                    <div
                                                        class="w-14 h-14 rounded-xl overflow-hidden flex-shrink-0
                                                bg-gray-100 flex items-center justify-center">
                                                        @if ($fnbItem->image)
                                                            <img src="{{ Storage::url($fnbItem->image) }}"
                                                                class="w-full h-full object-contain"
                                                                alt="{{ $fnbItem->name }}">
                                                        @else
                                                            <i class="ti ti-tools-kitchen-2 text-gray-300 text-xl"></i>
                                                        @endif
                                                    </div>

                                                    {{-- Info menu --}}
                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-gray-800 text-sm">
                                                            {{ $fnbItem->name }}
                                                        </p>
                                                        @if ($fnbItem->description)
                                                            <p class="text-xs text-gray-400 truncate mt-0.5">
                                                                {{ $fnbItem->description }}
                                                            </p>
                                                        @endif
                                                        <p class="text-xs font-bold mt-1" style="color:#16a34a;">
                                                            Rp {{ number_format($fnbItem->price, 0, ',', '.') }}
                                                        </p>
                                                    </div>

                                                    {{-- ==========================================
                                         QTY CONTROL
                                         Tombol + dan - update qty lokal
                                         lalu dispatch event ke parent
                                    ========================================== --}}
                                                    <div class="flex items-center gap-3 flex-shrink-0">

                                                        {{-- Tombol kurangi qty --}}
                                                        <button type="button"
                                                            @click="
                                                    if (qty > 0) {
                                                        qty--
                                                        $dispatch('fnb-update', {
                                                            id:    {{ $fnbItem->id }},
                                                            price: {{ $fnbItem->price }},
                                                            name:  '{{ addslashes($fnbItem->name) }}',
                                                            qty:   qty
                                                        })
                                                    }
                                                "
                                                            class="w-8 h-8 flex items-center justify-center rounded-full
                                                       border-2 transition"
                                                            :class="qty > 0 ?
                                                                'border-red-300 text-red-500 hover:bg-red-50' :
                                                                'border-gray-200 text-gray-300 cursor-not-allowed'">
                                                            <i class="ti ti-minus text-xs"></i>
                                                        </button>

                                                        {{-- DISPLAY QTY —
                                             pakai style inline untuk pastikan warna selalu tampil
                                             karena Tailwind JIT kadang tidak compile class conditional
                                        --}}
                                                        <div class="w-8 h-8 flex items-center justify-center rounded-full"
                                                            :style="qty > 0 ?
                                                                'background:#16a34a;' :
                                                                'background:#f3f4f6;'">
                                                            <span class="text-sm font-bold leading-none"
                                                                :style="qty > 0 ? 'color:white;' : 'color:#9ca3af;'"
                                                                x-text="qty">
                                                            </span>
                                                        </div>

                                                        {{-- Hidden input — nama kosong jika qty 0 --}}
                                                        {{-- Saat submit form, hanya terkirim jika qty > 0 --}}
                                                        <input type="hidden"
                                                            :name="qty > 0 ? 'fnb[{{ $fnbItem->id }}]' : ''"
                                                            :value="qty">

                                                        {{-- Tombol tambah qty --}}
                                                        <button type="button"
                                                            @click="
                                                    qty++
                                                    $dispatch('fnb-update', {
                                                        id:    {{ $fnbItem->id }},
                                                        price: {{ $fnbItem->price }},
                                                        name:  '{{ addslashes($fnbItem->name) }}',
                                                        qty:   qty
                                                    })
                                                "
                                                            class="w-8 h-8 flex items-center justify-center rounded-full
                                                       border-2 border-green-300 text-green-600
                                                       hover:bg-green-50 transition">
                                                            <i class="ti ti-plus text-xs"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </form>
            </div>

            {{-- ==========================================
             KOLOM KANAN — Ringkasan Harga (Sticky)
        ========================================== --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 sticky top-24">

                    <h3 class="font-bold text-gray-800 mb-4">Ringkasan Pesanan</h3>

                    {{-- Info kamar --}}
                    <div class="pb-4 border-b border-gray-100 mb-4">
                        <p class="font-semibold text-gray-700 text-sm">{{ $room->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">No. {{ $room->room_number }}</p>
                    </div>

                    {{-- Rincian harga --}}
                    <div class="space-y-2 mb-4">

                        {{-- Harga kamar --}}
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">
                                Rp {{ number_format($room->price_per_night, 0, ',', '.') }}
                                × <span x-text="nights"></span> malam
                            </span>
                            <span class="font-medium text-gray-800" x-text="formatRupiah(roomTotal)">
                            </span>
                        </div>

                        {{-- Item FnB yang dipilih — update real-time --}}
                        <template x-for="(item, id) in fnbItems" :key="id">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 truncate max-w-32" x-text="item.name + ' ×' + item.qty">
                                </span>
                                <span class="font-medium text-gray-800 flex-shrink-0 ml-2"
                                    x-text="formatRupiah(item.price * item.qty)">
                                </span>
                            </div>
                        </template>

                        {{-- Subtotal + pajak --}}
                        <div class="border-t border-gray-100 pt-2 space-y-1">
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span>Subtotal</span>
                                <span x-text="formatRupiah(grandTotal)"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span>Pajak (11%)</span>
                                <span x-text="formatRupiah(grandTotal * 0.11)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Grand total --}}
                    <div class="flex items-center justify-between py-3 border-t border-gray-200 mb-5">
                        <span class="font-bold text-gray-800">Total Bayar</span>
                        <span class="font-bold text-xl" style="color:#16a34a;"
                            x-text="formatRupiah(grandTotal * 1.11)">
                        </span>
                    </div>

                    {{-- Tombol submit --}}
                    <button type="submit" form="booking-form"
                        class="w-full py-3 text-sm font-bold text-white rounded-xl transition"
                        style="background:#16a34a;" :disabled="nights < 1"
                        :class="nights < 1 ?
                            'opacity-50 cursor-not-allowed' :
                            'hover:-translate-y-0.5'">
                        <i class="ti ti-calendar-plus mr-2"></i>
                        Lanjutkan Pemesanan
                    </button>

                    {{-- Info jika tanggal belum dipilih --}}
                    <p class="text-xs text-center text-gray-400 mt-2" x-show="nights < 1">
                        Pilih tanggal check-in & check-out dulu
                    </p>

                    <p class="text-xs text-gray-400 text-center mt-3 flex items-center justify-center gap-1">
                        <i class="ti ti-lock text-xs"></i>
                        Pembayaran aman via Midtrans
                    </p>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
