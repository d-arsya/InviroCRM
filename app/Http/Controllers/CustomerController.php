<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Message;
use App\Traits\ApiResponder;
use App\Traits\Whatsapp;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    use ApiResponder, Whatsapp;

    /**
     * Get customers data by datec
     *
     * @param  string  $date  YYYY-MM-DD ex. 2025-09-10
     */
    #[Group('Customer')]
    public function byDate(string $date)
    {
        $customers = Customer::with('message')->where('date', $date)->get();

        return $this->success(CustomerResource::collection($customers));
    }

    /**
     * Get customer data by order ID
     *
     * @param  string  $order_id  ex. ORD-250910-001
     */
    #[Group('Customer')]
    public function byOrder(Customer $customer)
    {
        return $this->success(new CustomerResource($customer));
    }

    /**
     * Edit the used template for a customer
     *
     * Hanya bisa apabila pesan belum terkirim
     */
    #[Group('Customer')]
    public function editMessage(Request $request, Customer $customer)
    {
        Gate::allowIf(function () use ($customer) {
            return $customer->status != 'sended';
        });
        $request->validate(['message_id' => 'required|integer|exists:messages,id']);
        $message = Message::find($request->message_id);
        $customer->update(['message_id' => $message->id, 'message_value' => [
            'text' => $message->text,
            'title' => $message->title,
        ]]);

        return $this->success(new CustomerResource($customer));
    }

    /**
     * Send failed message
     *
     * Hanya bisa apabila pesan gagal
     */
    #[Group('Customer')]
    public function sendMessage(Customer $customer)
    {
        Gate::allowIf(function () use ($customer) {
            return $customer->status == 'failed';
        });
        $isValid = $this->isWhatsapp($customer->phone);
        if ($isValid) {
            $message = $customer->message->text;
            $message = str_replace('{nama}', $customer->name, $message);
            $message = str_replace('\n', "\n", $message);
            $sended = $this->send($customer->phone, $message);
        }
        if ($sended) {
            $customer->update(['status' => 'sended']);

            return $this->success(null);
        }

        return $this->error(null);
    }
}
