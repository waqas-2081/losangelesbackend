<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Log;

class GmailApiService
{
    protected $service;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName(env('APP_NAME'));
        $client->setScopes(Gmail::GMAIL_SEND);
        $client->setAuthConfig(storage_path('app/gmail-oauth-credentials.json')); // OAuth credentials file
        $client->setAccessType('offline');

        // Load token
        $tokenPath = storage_path('app/gmail-token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);

            // Refresh token if expired
            if ($client->isAccessTokenExpired() && isset($accessToken['refresh_token'])) {
                Log::info('Access token expired, refreshing...');
                $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                Log::info('Access token refreshed successfully');
            }
        } else {
            throw new \Exception('No access token found. Please run the authorization process first.');
        }

        $this->service = new Gmail($client);
        Log::info('Gmail API service initialized successfully');
    }

    /**
     * Send raw MIME email
     */
    public function sendRawEmail(string $rawMessage)
    {
        try {
            $mime = rtrim(strtr(base64_encode($rawMessage), '+/', '-_'), '=');
            $message = new Message();
            $message->setRaw($mime);
            
            $result = $this->service->users_messages->send('me', $message);
            
            Log::info('Email sent successfully via Gmail API', [
                'message_id' => $result->getId()
            ]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send email via Gmail API', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }

    /**
     * Send email with individual parameters
     */
    public function sendEmail(string $to, string $subject, string $body, string $fromName = null)
    {
        $fromName = $fromName ?: env('GMAIL_FROM_NAME', env('APP_NAME'));
        $fromEmail = env('GMAIL_FROM_EMAIL');
        
        $rawMessage = "From: {$fromName} <{$fromEmail}>\r\n";
        $rawMessage .= "To: {$to}\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "MIME-Version: 1.0\r\n";
        $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $rawMessage .= $body;

        return $this->sendRawEmail($rawMessage);
    }

    /**
     * Send email to multiple recipients
     */
    public function sendEmailToMultiple(array $recipients, string $subject, string $body, string $fromName = null)
    {
        $fromName = $fromName ?: env('GMAIL_FROM_NAME', env('APP_NAME'));
        $fromEmail = env('GMAIL_FROM_EMAIL');
        
        $rawMessage = "From: {$fromName} <{$fromEmail}>\r\n";
        $rawMessage .= "To: " . implode(',', $recipients) . "\r\n";
        $rawMessage .= "Subject: {$subject}\r\n";
        $rawMessage .= "MIME-Version: 1.0\r\n";
        $rawMessage .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $rawMessage .= $body;

        return $this->sendRawEmail($rawMessage);
    }

    /**
     * Test the Gmail API connection
     */
    public function testConnection()
    {
        try {
            $profile = $this->service->users->getProfile('me');
            
            Log::info('Gmail API connection test successful', [
                'email' => $profile->getEmailAddress()
            ]);
            
            return [
                'success' => true,
                'email' => $profile->getEmailAddress(),
                'messages_total' => $profile->getMessagesTotal()
            ];
            
        } catch (\Exception $e) {
            Log::error('Gmail API connection test failed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get the authorization URL for initial setup
     */
    public static function getAuthUrl()
    {
        $client = new Client();
        $client->setApplicationName(env('APP_NAME'));
        $client->setScopes(Gmail::GMAIL_SEND);
        $client->setAuthConfig(storage_path('app/gmail-oauth-credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
        
        return $client->createAuthUrl();
    }

    /**
     * Exchange authorization code for access token
     */
    public static function exchangeAuthCode($authCode)
    {
        try {
            $client = new Client();
            $client->setApplicationName(env('APP_NAME'));
            $client->setScopes(Gmail::GMAIL_SEND);
            $client->setAuthConfig(storage_path('app/gmail-oauth-credentials.json'));
            $client->setAccessType('offline');
            $client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
            
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            
            if (isset($accessToken['error'])) {
                throw new \Exception('Error fetching access token: ' . $accessToken['error_description']);
            }
            
            // Save the token
            $tokenPath = storage_path('app/gmail-token.json');
            file_put_contents($tokenPath, json_encode($accessToken));
            
            Log::info('Access token saved successfully', [
                'token_path' => $tokenPath,
                'has_refresh_token' => isset($accessToken['refresh_token'])
            ]);
            
            return $accessToken;
            
        } catch (\Exception $e) {
            Log::error('Failed to exchange auth code for token', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}