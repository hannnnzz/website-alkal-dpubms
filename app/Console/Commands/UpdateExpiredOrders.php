<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class UpdateExpiredOrders extends Command
{
    // Nama command yang akan dipanggil di schedule
    protected $signature = 'orders:update-expired';

    // Deskripsi command
    protected $description = 'Update order status to EXPIRED if unpaid and timeout (15 minutes)';

    public function handle()
    {
        $expiredTime = Carbon::now()->subMinutes(15);

        // 1) Expire orders UNPAID/PENDING yang lewat 15 menit
        $orders = Order::whereIn('status', ['UNPAID', 'PENDING'])
            ->where('created_at', '<', $expiredTime)
            ->get();

        foreach ($orders as $order) {
            // release alat yang terkait order items (type = Sewa)
            foreach ($order->items()->where('type', 'Sewa')->get() as $item) {
                if ($item->alat_id) {
                    $alat = \App\Models\AlatSewaType::find($item->alat_id);
                    if ($alat) {
                        // Hanya release jika locked_until terkait atau jika tidak ada overlap lain
                        $alat->update(['is_locked' => false, 'locked_until' => null]);
                    }
                }
            }
            $order->update(['status' => 'EXPIRED']);
            $this->info("Order ID {$order->id} diupdate jadi EXPIRED");
        }

        // 2) Release alat yang locked_until sudah lewat (mis. rental selesai)
        $expiredLocks = \App\Models\AlatSewaType::where('is_locked', true)
            ->whereNotNull('locked_until')
            ->where('locked_until', '<', now())
            ->get();

        foreach ($expiredLocks as $alat) {
            $alat->update(['is_locked' => false, 'locked_until' => null]);
            $this->info("Alat ID {$alat->id} dilepas karena locked_until lewat.");
        }

        $this->info('Update expired orders & release locks selesai.');
    }

}
