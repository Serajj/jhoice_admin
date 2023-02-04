<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Booking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StatusChangedBooking;

class CancelBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $bk;
    public $providers;
    public $users;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bk, $providers, $users)
    {
        $this->bk = $bk;
        $this->providers = $providers;
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $booking = Booking::find($this->bk);
        if($booking){
          if($booking->booking_status_id < 2){
            $booking->booking_status_id = 7;
            $booking->cancel = true;
            $booking->save();

            ///var/www/vhosts/jhoice.com/public_html

            Notification::send($this->users, new StatusChangedBooking($booking));
            Notification::send($this->providers, new StatusChangedBooking($booking));
          }

        }
    }
}
