<?php

namespace App\Traits;

use App\Models\WhatsappToken;

trait Whatsapp
{
    protected function send($target, $message, $delay = 2)
    {
        $token = $this->getToken();
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
                'schedule' => 0,
                'typing' => false,
                'delay' => $delay,
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: '.$token,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response)->status;
    }

    private function getToken()
    {
        $tokens = WhatsappToken::whereActive(true)->orderBy('id')->get();
        $currentIndex = $tokens->search(function ($token) {
            return $token->used === true;
        });

        if ($currentIndex !== false) {
            $tokens[$currentIndex]->used = false;
            $tokens[$currentIndex]->save();
        }

        $nextIndex = ($currentIndex === false || $currentIndex + 1 >= $tokens->count())
            ? 0
            : $currentIndex + 1;

        $tokens[$nextIndex]->used = true;
        $tokens[$nextIndex]->save();

        return $tokens[$nextIndex]->token;
    }

    protected function isWhatsapp($number)
    {
        $token = $this->getToken();
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $number,
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: '.$token,
            ],
        ]);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        $res = $response->registered[0] ?? 'false';

        return $res != 'false';
    }

    protected function isWhatsappBulk($number)
    {
        $token = $this->getToken();
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/validate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $number,
                'countryCode' => '62',
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: '.$token,
            ],
        ]);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        return $response;
    }
}
