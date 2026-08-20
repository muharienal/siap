            <div id="roomFacilitiesList">
                @if($room->facilities->count() > 0)
                    @foreach($room->facilities as $f)
                        <div class="facility-list-item">
                            <div class="facility-info">
                                <div class="facility-icon"><i class="bi bi-box"></i></div>
                                <div>
                                    <div class="facility-name">{{ $f->name }}</div>
                                    <div class="facility-qty">Jumlah: {{ $f->pivot->quantity }} unit</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-photos" style="padding: 1.5rem;">
                        <i class="bi bi-inbox d-block fs-4 mb-2"></i>
                        Belum ada fasilitas terpasang.
                    </div>
                @endif
            </div>
