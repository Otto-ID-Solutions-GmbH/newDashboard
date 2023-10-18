<?php

namespace Cintas\Mail;

use Carbon\Carbon;
use Cintas\Exceptions\ExternalDeviceException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $message;
    private $severity;
    private $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\Exception $exception = null)
    {
        $this->severity = $exception->severity ?? 'error';

        if ($exception instanceof ExternalDeviceException) {
            $this->type = 'External Device Error';
        } else {
            $this->type = 'Internal Server Error';
        }

        $this->message = $exception->getMessage() ?? 'No Message...';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('columbus@cintas.visbos.com')
            ->subject($this->severity == 'critical' ? 'CRITICAL ERROR at Cintas/Columbus' : 'Error at Cintas/Columbus')
            ->markdown('emails.errors.devices', [
                'severity' => $this->severity,
                'type' => $this->type,
                'message' => $this->message,
                'timestamp' => Carbon::now('Europe/Berlin')
            ]);
    }
}
