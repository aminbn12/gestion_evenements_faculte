<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    protected ?Client $client;
    protected string $from;

    public function __construct()
    {
        $this->from = config('services.twilio.whatsapp_from');
        
        if (config('services.twilio.sid') && config('services.twilio.token')) {
            $this->client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        }
    }

    /**
     * Send a WhatsApp message.
     */
    public function sendMessage(string $to, string $message): bool
    {
        // If Twilio is not configured, use CallMeBot as fallback
        if (!$this->client) {
            return $this->sendViaCallMeBot($to, $message);
        }

        try {
            // Format phone number
            $to = $this->formatPhoneNumber($to);

            $this->client->messages->create(
                "whatsapp:{$to}",
                [
                    'from' => $this->from,
                    'body' => $message,
                ]
            );

            return true;

        } catch (\Exception $e) {
            \Log::error('WhatsApp sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send message via CallMeBot (free alternative).
     */
    protected function sendViaCallMeBot(string $phone, string $message): bool
    {
        $apiKey = config('services.callmebot.api_key');

        if (!$apiKey) {
            \Log::warning('WhatsApp not configured: No API key set');
            return false;
        }

        try {
            $phone = $this->formatPhoneNumber($phone);
            $message = urlencode($message);
            
            $url = "https://api.callmebot.com/whatsapp.php?phone={$phone}&text={$message}&apikey={$apiKey}";
            
            $response = @file_get_contents($url);
            
            return $response !== false;

        } catch (\Exception $e) {
            \Log::error('CallMeBot sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number for WhatsApp.
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add Morocco country code if not present
        if (!str_starts_with($phone, '212')) {
            if (str_starts_with($phone, '0')) {
                $phone = '212' . substr($phone, 1);
            } else {
                $phone = '212' . $phone;
            }
        }

        return $phone;
    }
}
