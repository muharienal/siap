@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp
    <div class="card-body" id="scheduleBody">
        @php
            $slotHeight = 64;
            $pxPerMin = $slotHeight / 30;
            $slotCount = count($timeSlots ?? []);
            $gridHeight = ($slotCount + 1) * $slotHeight;

            $dayStartParts = explode(':', $timeSlots[0] ?? '07:00');
            $dayStartMinutes = ((int) $dayStartParts[0]) * 60 + ((int) $dayStartParts[1]);

            $nowMinutes = Carbon::now()->hour * 60 + Carbon::now()->minute;
            $nowTopPx = ($nowMinutes - $dayStartMinutes) * $pxPerMin;
            $showNowLine = ($isToday ?? false) && !($isWeekend ?? false) && $nowTopPx >= 0 && $nowTopPx <= $gridHeight;

            // Warna status kotak jadwal — NILAINYA HARUS SAMA PERSIS dengan
            // variabel CSS --status-pending-*/--status-approved-*/
            // --status-rejected-* yang didefinisikan di <style> dashboard.
            // blade.php (PHP tidak bisa langsung membaca custom property
            // CSS, jadi disalin manual di sini — kalau salah satu diubah,
            // ubah juga yang satunya supaya warna kotak jadwal & legend
            // tetap sama).
            $statusColors = [
                0 => ['bg' => 'rgba(245,158,11,0.10)', 'border' => '#f59e0b', 'text' => '#b45309', 'chipBg' => 'rgba(245,158,11,0.18)', 'label' => 'Pending'],
                1 => ['bg' => 'rgba(16,185,129,0.10)', 'border' => '#10b981', 'text' => '#047857', 'chipBg' => 'rgba(16,185,129,0.18)', 'label' => 'Disetujui'],
                2 => ['bg' => 'rgba(239,68,68,0.08)',  'border' => '#ef4444', 'text' => '#b91c1c', 'chipBg' => 'rgba(239,68,68,0.18)', 'label' => 'Ditolak'],
                // Status 2 yang berasal dari persetujuan yang DIBATALKAN (lihat Booking::was_cancelled_approval)
                'cancelled' => ['bg' => 'rgba(100,116,139,0.08)', 'border' => '#64748b', 'text' => '#475569', 'chipBg' => 'rgba(100,116,139,0.18)', 'label' => 'Dibatalkan'],
            ];
        @endphp

        @if($slotCount > 0 && count($rooms ?? []) > 0)
            @php $gridCols = "80px repeat({$rooms->count()}, minmax(200px, 1fr))"; @endphp

            <div class="cal-grid-header" style="grid-template-columns: {{ $gridCols }};">
                <div class="cal-header-cell cal-time-header"></div>
                @foreach($rooms as $room)
                    @php
                        $photos = $room->photos ?? collect();
                        $hasPhoto = $photos->count() > 0;
                        $firstPhoto = $hasPhoto ? $photos->first()->photo_url : null;
                    @endphp
                    <div class="cal-header-cell cal-room-header">
                        @if($hasPhoto)
                            <img src="{{ $firstPhoto }}" class="cal-room-photo" tabindex="0"
                                 onclick="openGallery({{ $room->id }})"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openGallery({{ $room->id }});}"
                                 alt="Lihat foto {{ $room->name }}" title="Klik untuk lihat foto" loading="lazy">
                        @else
                            <div class="cal-room-photo-placeholder"><i class="bi bi-building"></i></div>
                        @endif
                        <div class="cal-room-info">
                            <div class="cal-room-name">{{ $room->name }}</div>
                            <div class="cal-room-meta-row">
                                <span class="cal-room-capacity"><i class="bi bi-people-fill"></i> {{ $room->capacity }} orang</span>
                                <span class="cal-room-location"><i class="bi bi-geo-alt-fill"></i> {{ $room->location }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="cal-grid-body-wrapper">
                <div class="cal-grid-body" style="grid-template-columns: {{ $gridCols }};">
                    <div class="cal-time-col" style="height: {{ $gridHeight }}px;">
                        @foreach($timeSlots as $timeSlot)
                            @php $isHour = Str::endsWith($timeSlot, ':00'); @endphp
                            <div class="cal-time-label {{ $isHour ? 'hour-mark' : '' }}" style="height: {{ $slotHeight }}px;">{{ $timeSlot }}</div>
                        @endforeach
                    </div>

                    @foreach($rooms as $room)
                        <div class="cal-room-col {{ $isWeekend ? 'cal-weekend' : '' }}" style="height: {{ $gridHeight }}px;">
                            @if($isWeekend)
                                <div class="cal-weekend-overlay"><i class="bi bi-calendar-x"></i> Libur</div>
                            @else
                                @foreach($timeSlots as $index => $timeSlot)
                                    @php
                                        // Cek apakah slot waktu sudah selesai (lewat dari waktu sekarang)
                                        // Jika waktu sekarang masih di dalam rentang slot (start - end), maka masih bisa dipesan.
                                        $slotStart = Carbon::parse(($selectedDate ?? date('Y-m-d')) . ' ' . $timeSlot);
                                        $slotEnd = $slotStart->copy()->addMinutes(30); // Interval 30 menit
                                        $isPastSlot = $slotEnd->lte(Carbon::now());
                                    @endphp
                                    
                                    @if($isPastSlot)
                                        <div class="cal-slot-past" style="top: {{ $index * $slotHeight }}px; height: {{ $slotHeight }}px;" title="Waktu telah berlalu">
                                            <i class="bi bi-lock-fill"></i>
                                        </div>
                                    @else
                                        <div class="cal-slot-hover" role="button" tabindex="0"
                                             style="top: {{ $index * $slotHeight }}px; height: {{ $slotHeight }}px;"
                                             onclick="quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}')"
                                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}');}"
                                             aria-label="Booking {{ $room->name }} jam {{ $timeSlot }}"
                                             title="Klik untuk booking jam {{ $timeSlot }}">
                                            <div class="cal-slot-hover-inner"><i class="bi bi-plus-lg"></i> Booking</div>
                                        </div>
                                    @endif
                                @endforeach

                                @if(isset($visibleBookings))
                                    @php
                                        // FIX: sebelumnya SEMUA booking di ruangan ini digambar dengan
                                        // left/right yang sama persis (selebar kolom penuh) — kalau ada
                                        // 2 booking yang jam-nya beririsan di ruangan yang sama (ini
                                        // SAH terjadi sekarang: booking Priority boleh menembus booking
                                        // Reguler yang bentrok, keduanya tetap status 0/1 sampai admin
                                        // mengalihkan salah satunya), kotaknya saling menimpa persis di
                                        // atas satu sama lain — teks jadi bertumpuk tidak terbaca.
                                        //
                                        // Solusinya: bagi jadi "lane" berdampingan (algoritma umum
                                        // kalender concurrent-events) — setiap booking yang jam-nya
                                        // beririsan dengan booking lain di ruangan yang sama digambar
                                        // berdampingan (lebih sempit), bukan bertumpuk. Booking yang
                                        // jamnya tidak beririsan dengan siapa pun tetap selebar kolom
                                        // penuh seperti biasa.
                                        $roomBookings = $visibleBookings->where('room_id', $room->id)->sortBy('start_time')->values();
                                        $laneOf = [];
                                        $laneCountOf = [];
                                        $active = [];
                                        $clusterIds = [];
                                        $clusterMaxLane = -1;

                                        foreach ($roomBookings as $rb) {
                                            $rbS = Carbon::parse($rb->start_time);
                                            $rbE = Carbon::parse($rb->end_time);
                                            $rbSMin = $rbS->hour * 60 + $rbS->minute;
                                            $rbEMin = $rbE->hour * 60 + $rbE->minute;

                                            foreach ($active as $lane => $endMin) {
                                                if ($endMin <= $rbSMin) {
                                                    unset($active[$lane]);
                                                }
                                            }

                                            if (empty($active) && !empty($clusterIds)) {
                                                foreach ($clusterIds as $cid) {
                                                    $laneCountOf[$cid] = $clusterMaxLane + 1;
                                                }
                                                $clusterIds = [];
                                                $clusterMaxLane = -1;
                                            }

                                            $lane = 0;
                                            while (isset($active[$lane])) { $lane++; }

                                            $active[$lane] = $rbEMin;
                                            $laneOf[$rb->id] = $lane;
                                            $clusterIds[] = $rb->id;
                                            $clusterMaxLane = max($clusterMaxLane, $lane);
                                        }
                                        foreach ($clusterIds as $cid) {
                                            $laneCountOf[$cid] = $clusterMaxLane + 1;
                                        }
                                    @endphp
                                    @foreach($roomBookings as $booking)
                                        @php
                                            $bStart = Carbon::parse($booking->start_time);
                                            $bEnd = Carbon::parse($booking->end_time);
                                            $bStartMin = $bStart->hour * 60 + $bStart->minute;
                                            $bEndMin = $bEnd->hour * 60 + $bEnd->minute;
                                            $topPx = max(0, ($bStartMin - $dayStartMinutes) * $pxPerMin);
                                            $heightPx = max(28, ($bEndMin - $bStartMin) * $pxPerMin);
                                            $colors = $statusColors[$booking->status] ?? $statusColors[0];
                                            if ($booking->was_cancelled_approval) { $colors = $statusColors['cancelled']; }
                                            $bookerName = $booking->user->full_name ?? 'Unknown';
                                            $initial = strtoupper(mb_substr($bookerName, 0, 1));
                                            $isCompact = $heightPx < 52;
                                            $lanes = $laneCountOf[$booking->id] ?? 1;
                                            $lane = $laneOf[$booking->id] ?? 0;
                                            $laneLeftPct = ($lane / $lanes) * 100;
                                            $laneWidthPct = 100 / $lanes;
                                            $isCompact = $isCompact || $lanes > 1;
                                            // FIX: dulu semua kotak jadwal bisa diklik dan mengarah ke
                                            // halaman detail, walau ujung-ujungnya kena 403 kalau bukan
                                            // booking sendiri. Sekarang klik cuma aktif untuk booking
                                            // milik sendiri atau kalau yang lihat admin — booking orang
                                            // lain tetap kelihatan (untuk koordinasi jadwal ruangan),
                                            // tapi memang tidak bisa diklik sama sekali.
                                            $canOpen = Auth::user()->role == 1 || $booking->user_id == Auth::id();
                                            // FIX: sebelumnya Str::limit(...,60) memotong judul
                                            // paksa berdasarkan JUMLAH KARAKTER, tidak peduli
                                            // apakah box-nya sebenarnya cukup luas untuk
                                            // menampilkan lebih banyak teks — hasilnya "..."
                                            // sering muncul padahal masih ada ruang kosong di
                                            // kartu. CSS-nya sendiri (.cal-event-title) sudah
                                            // punya -webkit-line-clamp:2 yang otomatis
                                            // menyesuaikan ke tinggi kartu yang sebenarnya, jadi
                                            // pemotongan manual di PHP ini dihapus — biar CSS
                                            // yang menentukan, bukan angka karakter tetap.
                                            $bidangName = $booking->user->position->name ?? null;
                                        @endphp
                                        <div class="cal-event {{ $isCompact ? 'is-compact' : '' }} {{ !$canOpen ? 'is-readonly' : '' }}"
                                             @if($canOpen)
                                                 role="button" tabindex="0"
                                                 onclick="showBookingDetails({{ $booking->id }})"
                                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();showBookingDetails({{ $booking->id }});}"
                                                 aria-label="Detail booking {{ $booking->purpose }}"
                                             @endif
                                             style="top: {{ $topPx }}px; height: {{ $heightPx }}px; left: calc({{ $laneLeftPct }}% + 3px); width: calc({{ $laneWidthPct }}% - 6px); background: {{ $colors['bg'] }}; --event-accent: {{ $colors['border'] }};">
                                            @if($booking->booking_type === 'Priority' && Auth::user()->role == 1)
                                                <span class="cal-event-priority-flag" title="Priority">
                                                    <i class="bi bi-star-fill" style="color: var(--priority-solid);"></i>
                                                </span>
                                            @endif
                                            <div class="cal-event-top">
                                                <span class="cal-event-avatar" style="background: {{ $colors['border'] }};">{{ $initial }}</span>
                                                <span class="cal-event-title" style="color: {{ $colors['text'] }};">{{ $booking->purpose }}</span>
                                            </div>
                                            <div class="cal-event-sub"><i class="bi bi-person-fill" style="font-size:9px;opacity:0.6;margin-right:3px;"></i>{{ $bookerName }}</div>
                                            @if($bidangName)
                                                <div class="cal-event-sub cal-event-bidang"><i class="bi bi-diagram-3-fill" style="font-size:9px;opacity:0.6;margin-right:3px;"></i>{{ $bidangName }}</div>
                                            @endif
                                            <div class="cal-event-time"><i class="bi bi-clock"></i> {{ $bStart->format('H:i') }}–{{ $bEnd->format('H:i') }}</div>
                                            <span class="cal-event-status" style="color: {{ $colors['text'] }}; background: {{ $colors['chipBg'] }};">{{ $colors['label'] }}</span>

                                            {{-- Tooltip kaya informasi saat hover — menggantikan
                                                 title="..." bawaan browser (lambat muncul, polos,
                                                 tanpa styling). Selalu berisi info LENGKAP (judul,
                                                 peminjam, bidang, ruangan, jam, status) apa pun
                                                 ukuran kartunya — jadi walau kartu kecil/rapat
                                                 memotong tampilan, detail lengkapnya tetap bisa
                                                 dilihat jelas tinggal hover. --}}
                                            <div class="cal-event-tooltip">
                                                <div class="cal-event-tooltip-title">{{ $booking->purpose }}</div>
                                                <div class="cal-event-tooltip-row"><i class="bi bi-person-fill"></i> {{ $bookerName }}@if($bidangName) &middot; {{ $bidangName }}@endif</div>
                                                <div class="cal-event-tooltip-row"><i class="bi bi-door-open-fill"></i> {{ $room->name }}</div>
                                                <div class="cal-event-tooltip-row"><i class="bi bi-clock-fill"></i> {{ $bStart->format('H:i') }}–{{ $bEnd->format('H:i') }} WIB</div>
                                                <div class="cal-event-tooltip-row"><i class="bi bi-circle-fill" style="font-size:6px; color:{{ $colors['border'] }};"></i> {{ $colors['label'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($showNowLine)
                    <div class="cal-now-line" id="nowLine" style="top: {{ $nowTopPx }}px;"><span class="cal-now-dot"></span></div>
                @endif
            </div>

            {{-- ============================================================
                 AGENDA LIST (KHUSUS MOBILE) — pengganti grid horizontal
                 supaya terasa seperti native app di HP, bukan tabel desktop
                 yang diperkecil.
            ============================================================ --}}
            <div class="agenda-mobile">
                <div class="agenda-room-tabs">
                    @foreach($rooms as $i => $room)
                        <button type="button" class="agenda-room-tab {{ $i === 0 ? 'active' : '' }}"
                                onclick="switchAgendaRoom({{ $room->id }})" data-room-tab="{{ $room->id }}">
                            {{ $room->name }}
                        </button>
                    @endforeach
                </div>

                @foreach($rooms as $i => $room)
                    <div class="agenda-room-panel {{ $i === 0 ? 'active' : '' }}" data-room-panel="{{ $room->id }}">
                        @if($isWeekend)
                            <div class="agenda-empty"><i class="bi bi-calendar-x"></i> Libur — tidak ada jadwal hari ini</div>
                        @else
                            @php $roomBookings = $visibleBookings->where('room_id', $room->id)->sortBy('start_time'); @endphp
                            <div class="agenda-list">
                                @foreach($timeSlots as $timeSlot)
                                    @php
                                        $slotStart = Carbon::parse(($selectedDate ?? date('Y-m-d')) . ' ' . $timeSlot);
                                        $slotEnd = $slotStart->copy()->addMinutes(30);
                                        $isPastSlot = $slotEnd->lte(Carbon::now());
                                        $slotBooking = $roomBookings->first(function ($b) use ($slotStart) {
                                            return $slotStart->gte(Carbon::parse($b->start_time)) && $slotStart->lt(Carbon::parse($b->end_time));
                                        });
                                        $isBookingStartSlot = $slotBooking && Carbon::parse($slotBooking->start_time)->format('H:i') === $timeSlot;
                                    @endphp

                                    @if($slotBooking && !$isBookingStartSlot)
                                        {{-- lanjutan booking yang sama, sudah dirender di slot mulainya --}}
                                    @elseif($slotBooking && $isBookingStartSlot)
                                        @php
                                            $bStart = Carbon::parse($slotBooking->start_time);
                                            $bEnd = Carbon::parse($slotBooking->end_time);
                                            $colors = $statusColors[$slotBooking->status] ?? $statusColors[0];
                                            if ($slotBooking->was_cancelled_approval) { $colors = $statusColors['cancelled']; }
                                            $bookerName = $slotBooking->user->full_name ?? 'Unknown';
                                            $bidangName = $slotBooking->user->position->name ?? null;
                                            $canOpenSlot = Auth::user()->role == 1 || $slotBooking->user_id == Auth::id();
                                        @endphp
                                        <div class="agenda-item agenda-item-booked {{ !$canOpenSlot ? 'is-readonly' : '' }}"
                                             @if($canOpenSlot)
                                                 role="button" tabindex="0"
                                                 onclick="showBookingDetails({{ $slotBooking->id }})"
                                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();showBookingDetails({{ $slotBooking->id }});}"
                                             @endif
                                             style="--event-accent: {{ $colors['border'] }}; background: {{ $colors['bg'] }};">
                                            <div class="agenda-item-time">{{ $bStart->format('H:i') }}<span>{{ $bEnd->format('H:i') }}</span></div>
                                            <div class="agenda-item-body">
                                                <div class="agenda-item-title">
                                                    @if($slotBooking->booking_type === 'Priority' && Auth::user()->role == 1)<i class="bi bi-star-fill" style="color: var(--priority-solid);"></i>@endif
                                                    {{-- FIX: sebelumnya Str::limit(...,80) + CSS
                                                         line-clamp:2 memotong judul walau baris list
                                                         ini punya ruang lebar yang cukup untuk teks
                                                         panjang — dihapus, judul sekarang tampil
                                                         penuh apa adanya. --}}
                                                    <span class="title-text">{{ $slotBooking->purpose }}</span>
                                                </div>
                                                <div class="agenda-item-sub"><i class="bi bi-person"></i> {{ $bookerName }}@if($bidangName) <span class="agenda-item-sub-divider">&middot;</span> <i class="bi bi-diagram-3-fill"></i> {{ $bidangName }}@endif</div>
                                            </div>
                                            <span class="agenda-item-status" style="color: {{ $colors['text'] }}; background: {{ $colors['chipBg'] }};">{{ $colors['label'] }}</span>
                                        </div>
                                    @elseif($isPastSlot)
                                        <div class="agenda-item agenda-item-past">
                                            <div class="agenda-item-time">{{ $timeSlot }}</div>
                                            <div class="agenda-item-body"><span class="agenda-item-empty-label"><i class="bi bi-lock-fill"></i> Waktu lewat</span></div>
                                        </div>
                                    @else
                                        <div class="agenda-item agenda-item-available" role="button" tabindex="0"
                                             onclick="quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}')"
                                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();quickBook({{ $room->id }}, '{{ $selectedDate }}', '{{ $timeSlot }}');}">
                                            <div class="agenda-item-time">{{ $timeSlot }}</div>
                                            <div class="agenda-item-body"><span class="agenda-item-empty-label"><i class="bi bi-plus-circle"></i> Tersedia — ketuk untuk booking</span></div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-results">
                <i class="bi bi-inbox"></i>
                <p>Tidak ada jadwal untuk tanggal yang dipilih.</p>
            </div>
        @endif
    </div>