<?php

namespace CodeTech\EuPago\Events;

use CodeTech\EuPago\Models\CcTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CCTransactionPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The MbReference reference object.
     *
     * @var CcTransaction
     */
    public $reference;

    /**
     * MBReferencePaid constructor.
     *
     * @param CcTransaction $reference
     */
    public function __construct(CcTransaction $reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
