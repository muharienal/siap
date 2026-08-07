<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\RentRoomRepository;

class HandleExpiredRentRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rent-rooms:handle-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle expired rent rooms that passed start date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan rent rooms yang expired...');
        
        try {
            $repository = app(RentRoomRepository::class);
            $repository->handleExpiredRentRooms();
            
            $this->info('Pengecekan rent rooms yang expired selesai!');
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
