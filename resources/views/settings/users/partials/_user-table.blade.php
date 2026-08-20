<div class="section-card" id="userListCard">
    <div class="section-card-header">
        <div class="title">
            <i class="bi bi-people-fill"></i> Daftar Pengguna
            <span class="count">{{ $users->count() }} total</span>
        </div>
    </div>

    @if($users->count() > 0)
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama Lengkap</th>
                        <th>Divisi</th>
                        <th>Bidang</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $item)
                        @php
                            $roleMap = [
                                1 => ['label' => 'Admin', 'class' => 'admin'],
                                2 => ['label' => 'Karyawan', 'class' => 'karyawan'],
                                3 => ['label' => 'Sesdir', 'class' => 'sesdir'],
                            ];
                            $roleInfo = $roleMap[$item->role] ?? ['label' => 'Karyawan', 'class' => 'karyawan'];
                        @endphp
                        <tr>
                            <td>{{ $item->nip }}</td>
                            <td><strong>{{ $item->full_name }}</strong></td>
                            <td>{{ $item->division->name ?? '-' }}</td>
                            <td>{{ $item->position->name ?? '-' }}</td>
                            <td><span class="role-pill {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span></td>
                            <td>
                                @if($item->is_active)
                                    <span class="status-pill active">Aktif</span>
                                @else
                                    <span class="status-pill inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('settings.users.edit', $item->id) }}" class="action-btn edit" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('settings.users.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-people"></i>
            <p>Belum ada pengguna</p>
            <a href="{{ route('settings.users.create') }}" class="btn-primary-sm" style="display:inline-flex;">
                <i class="bi bi-plus-lg"></i> Tambah Pengguna Pertama
            </a>
        </div>
    @endif
</div>
