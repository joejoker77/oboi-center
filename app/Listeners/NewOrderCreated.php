<?php

namespace App\Listeners;

use App\Entities\Shop\Order;
use App\Services\Sms\SmsSender;
use App\Mail\Shop\NewOrderToAdmin;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;

class /payment/ implements ShouldQueue
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
        $this->mailer->to(env('MAIL_FROM_ADDRESS'))->send(new NewOrderToAdmin($order));
        $this->smsSender->sendSms(env('SMS_ADMIN_NUMBER'), 'На сайте "Обои Центр, был создан новый заказ. Требуется внимание менеджера"');
    }
}
