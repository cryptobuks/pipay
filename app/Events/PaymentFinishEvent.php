<?php
namespace App\Events;
use App\User;
use App\Payment;
use App\Events\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PaymentFinishEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;
    
    public $payment;    

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Payment $payment )
    {
        $this->payment = $payment;        
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['invoice-channel.'. $this->payment->invoice_id];
    }

    /**
     * Get the broadcast event name.
     * 
     * @return array
     */
    public function broadcastAs()
    {
        return ['invoice.payment-finish'];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    
    public function broadcastWith()
    {
        return [
        'id' => $this->payment->id , 
        ];
    }


}