<?php

namespace App\Http\Controllers;

use App\Enums\OrderPaymentMethods;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ConsultationAppointmentTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Omnipay\Omnipay;

class CheckoutController extends Controller
{

    /**
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            $amount = 0;
            $cartItems = [];
            $userId = $request->user()->id;

            foreach ($request->consultation_appointment_times as $appointmentTime) {
                /** @var ConsultationAppointmentTime $consultationAppointmentTime */
                $consultationAppointmentTime = ConsultationAppointmentTime::find($appointmentTime, ['id', 'price']);
                $consultationAppointmentTime->status = 'reserved';
                $consultationAppointmentTime->save();

                $amount += $consultationAppointmentTime->price;

                $cartItem = new CartItem();
                $cartItem->product_id = $consultationAppointmentTime->id;
                $cartItem->user_id = $userId;

                $cartItems[] = $cartItem;
            }

            $order = new Order();
            $order->user_id = $userId;
            $order->items_amount = $amount;
            $order->payment_methods = OrderPaymentMethods::cases();

            /** @var \Omnipay\Stripe\Gateway $gateway */
            $gateway = Omnipay::create('Stripe');
            $gateway->setApiKey('sk_test_51MaTmCJtc69aInenGCb74m8UFq1uMXxeh6WjLbEQmJlkem4DczEkibt9P7M5UmOkTm4cgzRpJU90PKGvgAcN53cY008jPhW3yp');

            $order->save();

            foreach ($cartItems as $cartItem) {
                $cartItem->order_id = $order->id;
            }

            CartItem::insert($cartItems);
        });
    }
}
