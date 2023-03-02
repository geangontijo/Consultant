<?php

namespace App\Http\Controllers;

use App\Enums\OrderPaymentMethods;
use App\Enums\OrderPaymentStipeMethods;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ConsultationAppointmentTime;
use App\Models\OrderPayment;
use App\Services\HttpClient\HttpClientStripe;
use GuzzleHttp\BodySummarizerInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Omnipay\Common\Http\Client;
use Omnipay\Omnipay;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * @throws Throwable
     */
    public function store(Request $request)
    {
        /** @var PendingRequest $httpClientStripe */
        $httpClientStripe = Http::stripe();

        return DB::transaction(function () use ($httpClientStripe, $request) {
            $amount = 0;
            $cartItems = [];
            $paymentMethods = [];
            $userId = $request->user()->id;

            foreach ($request->consultation_appointment_times as $appointmentTime) {
                /** @var ConsultationAppointmentTime $consultationAppointmentTime */
                $consultationAppointmentTime = ConsultationAppointmentTime::find(
                    $appointmentTime,
                    ['id', 'price']
                );
                $consultationAppointmentTime->status = 'reserved';
                $consultationAppointmentTime->user()->associate($request->user());
                $consultationAppointmentTime->save();

                $amount += $consultationAppointmentTime->price;

                $cartItem = new CartItem();
                $cartItem->product_id = $consultationAppointmentTime->id;
                $cartItem->user_id = $userId;

                $cartItems[] = $cartItem;
            }

            $order = new Order();
            $order->id = $orderUuid = uuid_create(UUID_TYPE_RANDOM);
            $order->getAttribute('id');
            $order->user()->associate($request->user());
            $order->items_amount = $amount;

            // TODO: block payment lower then R$ 0,50. (boleto minimum R$ 5,00)

            $boletoAmount = $amount + 3.45;
            $bankSplitResponse = $httpClientStripe->post('payment_intents', [
                'amount' => round($boletoAmount * 100),
                'currency' => 'BRL',
                'confirm' => 'true',
                'payment_method_types' => [OrderPaymentStipeMethods::BankSlip->value],
                'payment_method_data' => [
                    'type' => OrderPaymentStipeMethods::BankSlip->value,
                    'boleto' => [
                        'tax_id' => '31505553024',
                    ],
                    'billing_details' => [
                        'name' => 'Jenny Rosen',
                        'email' => 'teste@gmail.com',
                        'address' => [
                            'line1' => '510 Townsend St',
                            'postal_code' => '25523166',
                            'city' => 'San Francisco',
                            'state' => 'MG',
                            'country' => 'BR',
                        ],
                    ],
                ],
            ])->json();

            $paymentMethods[] = new OrderPayment([
                'method' => OrderPaymentMethods::BankSlip,
                'transaction_id' => $bankSplitResponse['id'],
                'amount' => $boletoAmount,
                'metadata' => [
                    'barcode' => $bankSplitResponse['next_action']['boleto_display_details']['number'],
                    'pdf' => $bankSplitResponse['next_action']['boleto_display_details']['pdf'],
                ]
            ]);

            $creditCardAmount = $amount + 0.39 + ($amount * 0.04);
            $creditCardResponse = $httpClientStripe->post('payment_intents', [
                'amount' => round($creditCardAmount * 100),
                'currency' => 'BRL',
                'payment_method_types' => [OrderPaymentStipeMethods::CreditCard->value]
            ])->json();

            $paymentMethods[] = new OrderPayment([
                'method' => OrderPaymentMethods::CreditCard,
                'transaction_id' => $creditCardResponse['id'],
                'amount' => $creditCardAmount,
            ]);

            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethod->order()->associate($order);
            }

            $order->save();
            $order->id = $orderUuid;
            $order->payments()->createMany(array_map(fn ($paymentMethod) => $paymentMethod->toArray(), $paymentMethods));

            foreach ($cartItems as $cartItem) {
                $cartItem->order_id = $order->id;
            }

            CartItem::insert(array_map(fn ($cartItem) => $cartItem->toArray(), $cartItems));

            $order->getRelationValue('payments');

            return Redirect::route('checkout', ['order' => $orderUuid]);
        });
    }

    public function pay(string $order, Request $request)
    {
        /** @var PendingRequest $httpClientStripe */
        $httpClientStripe = Http::stripe();
//        $orderPayment = OrderPayment::findOrFail($request->payment_method_id);

//        $httpClientStripe->post('payment_intents/' . $order->payments()->where('method', OrderPaymentMethods::CreditCard)->first()->transaction_id . '/confirm', [
//            'payment_method' => $orderPayment->transaction_id,
//            'shipping' => [
//                'name' => 'Jenny Rosen',
//                'address' => [
//                    'line1' => '510 Townsend St',
//                    'postal_code' => '25523166',
//                    'city' => 'San Francisco',
//                    'state' => 'MG',
//                    'country' => 'BR',
//                ],
//            ],
//            'payment_method_options' => [
//                'card' => [
//                    'cvc_token' => $request->cvc_token,
//                ],
//            ]
//        ]);
        return Redirect::route('home')->with('success', 'Pagamento realizado com sucesso');
    }
}
