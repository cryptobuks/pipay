<?php
namespace App\Events;
use App\User;
use App\Invoice;
use App\Events\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InvoiceFinishEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;
    
    public $invoice;    

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Invoice $invoice )
    {
        $this->invoice = $invoice;        
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['invoice-channel.'. $this->invoice->id];
    }

    /**
     * Get the broadcast event name.
     * 
     * @return array
     */
    public function broadcastAs()
    {
        return ['invoice.payment-start'];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    
    public function broadcastWith()
    {
        return [
        'id' => $this->invoice->id , 
        ];
    }


}