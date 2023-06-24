<?php

namespace App\Listeners;

use App\Entities\Shop\Order;
use App\Services\Sms\SmsSender;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrderCreated implements ShouldQueue
{

    private SmsSender $smsSender;

    private Mailer $mailer;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SmsSender $smsSender, Mailer $mailer)
    {
        $this->smsSender = $smsSender;
        $this->mailer    = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param Order $order
     * @return void
     */
    public function handle(Order $order):void
    {


        $this->mailer->to(env('MAIL_FROM_ADDRESS'))->send(new VerifyMail($user));
    }
}
