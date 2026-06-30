<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\GmailApiService;

class AuthControllerGmail extends Controller
{
    private $gmailApiService;
    
    public function __construct()
    {
        // Initialize Gmail API service
        try {
            $this->gmailApiService = new GmailApiService();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Gmail API service in AuthController', [
                'error' => $e->getMessage()
            ]);
            $this->gmailApiService = null;
        }
    }
    
    
    public function getGmailAuthUrl()
    {
        try {
            // Create client directly without requiring existing token
            $client = new \Google\Client();
            $client->setApplicationName(env('APP_NAME'));
            $client->setScopes([\Google\Service\Gmail::GMAIL_SEND]);
            $client->setAuthConfig(storage_path('app/gmail-oauth-credentials.json'));
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            
            // For web application, use a specific redirect URI
            $redirectUri = env('APP_URL') . 'gmail-auth-callback-redirect';
            $client->setRedirectUri($redirectUri);
            
            $authUrl = $client->createAuthUrl();
            
            return response()->json([
                'success' => true,
                'auth_url' => $authUrl,
                'redirect_uri_configured' => $redirectUri,
                'instructions' => [
                    '1. Click the auth_url above',
                    '2. Sign in with your info@designslegend.com Google account', 
                    '3. Grant permissions to the application',
                    '4. You will be redirected back automatically',
                    '5. The authorization will be completed automatically'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get Gmail auth URL', [
                'error' => $e->getMessage(),
                'credentials_path' => storage_path('app/gmail-oauth-credentials.json'),
                'file_exists' => file_exists(storage_path('app/gmail-oauth-credentials.json'))
            ]);
            
            return response()->json([
                'error' => 'Failed to get authorization URL',
                'message' => $e->getMessage(),
                'check' => 'Make sure gmail-oauth-credentials.json exists in storage/app/',
                'app_url' => env('APP_URL')
            ], 500);
        }
    }

    /**
     * Step 2: Handle the redirect from Google (for web application)
     */
    public function handleGmailAuthRedirect(Request $request)
    {
        $authCode = $request->input('code');
        $error = $request->input('error');
        
        if ($error) {
            Log::error('OAuth authorization error', [
                'error' => $error,
                'error_description' => $request->input('error_description')
            ]);
            
            return response()->json([
                'error' => 'Authorization failed',
                'message' => $error,
                'description' => $request->input('error_description')
            ], 400);
        }
        
        if (!$authCode) {
            return response()->json([
                'error' => 'No authorization code received',
                'received_params' => $request->all()
            ], 400);
        }
        
        try {
            // Create client directly for token exchange
            $client = new \Google\Client();
            $client->setApplicationName(env('APP_NAME'));
            $client->setScopes([\Google\Service\Gmail::GMAIL_SEND]);
            $client->setAuthConfig(storage_path('app/gmail-oauth-credentials.json'));
            $client->setAccessType('offline');
            $client->setRedirectUri(env('APP_URL') . 'gmail-auth-callback-redirect');
            
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            
            if (isset($accessToken['error'])) {
                throw new \Exception('Error fetching access token: ' . $accessToken['error_description']);
            }
            
            // Save the token
            $tokenPath = storage_path('app/gmail-token.json');
            file_put_contents($tokenPath, json_encode($accessToken));
            
            Log::info('Access token saved successfully', [
                'token_path' => $tokenPath,
                'has_refresh_token' => isset($accessToken['refresh_token']),
                'expires_in' => $accessToken['expires_in'] ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gmail API authorization successful!',
                'token_saved' => true,
                'has_refresh_token' => isset($accessToken['refresh_token']),
                'next_step' => 'You can now use the contact form - emails will be sent via Gmail API'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to handle Gmail auth redirect', [
                'error' => $e->getMessage(),
                'auth_code' => substr($authCode, 0, 10) . '...',
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'error' => 'Failed to exchange authorization code',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alternative: Handle POST callback (manual code submission)
     */
    public function handleGmailAuthCallback(Request $request)
    {
        $authCode = $request->input('code');
        
        if (!$authCode) {
            return response()->json([
                'error' => 'Authorization code is required',
                'usage' => 'POST /gmail-auth-callback with {"code": "your_auth_code_here"}'
            ], 400);
        }
        
        try {
            // Create client directly for token exchange
            $client = new \Google\Client();
            $client->setApplicationName(env('APP_NAME'));
            $client->setScopes([\Google\Service\Gmail::GMAIL_SEND]);
            $client->setAuthConfig(storage_path('app/gmail-oauth-credentials.json'));
            $client->setAccessType('offline');
            $client->setRedirectUri(env('APP_URL') . 'gmail-auth-callback-redirect');
            
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            
            if (isset($accessToken['error'])) {
                throw new \Exception('Error fetching access token: ' . $accessToken['error_description']);
            }
            
            // Save the token
            $tokenPath = storage_path('app/gmail-token.json');
            file_put_contents($tokenPath, json_encode($accessToken));
            
            Log::info('Access token saved successfully via manual callback', [
                'token_path' => $tokenPath,
                'has_refresh_token' => isset($accessToken['refresh_token'])
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Gmail API authorization successful!',
                'token_saved' => true,
                'next_step' => 'You can now use the contact form - emails will be sent via Gmail API'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to handle Gmail auth callback', [
                'error' => $e->getMessage(),
                'auth_code' => substr($authCode, 0, 10) . '...'
            ]);
            
            return response()->json([
                'error' => 'Failed to exchange authorization code',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check the current authentication status
     */
    public function checkGmailAuthStatus()
    {
        try {
            $tokenPath = storage_path('app/gmail-token.json');
            
            if (!file_exists($tokenPath)) {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'No access token found. Please run the authorization process.',
                    'next_step' => 'Visit /gmail-auth-url to start authorization',
                    'credentials_exist' => file_exists(storage_path('app/gmail-oauth-credentials.json'))
                ]);
            }
            
            // Try to initialize the service to test the token
            try {
                $gmailService = new GmailApiService();
                $connectionTest = $gmailService->testConnection();
                
                return response()->json([
                    'authenticated' => $connectionTest['success'],
                    'connection_test' => $connectionTest,
                    'token_exists' => true,
                    'token_path' => $tokenPath
                ]);
            } catch (\Exception $serviceError) {
                return response()->json([
                    'authenticated' => false,
                    'token_exists' => true,
                    'error' => 'Token exists but service initialization failed',
                    'message' => $serviceError->getMessage(),
                    'next_step' => 'Try re-authorizing at /gmail-auth-url'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => false,
                'error' => $e->getMessage(),
                'next_step' => 'Visit /gmail-auth-url to re-authorize'
            ]);
        }
    }

    /**
     * Clear stored tokens (for debugging)
     */
    public function clearGmailTokens()
    {
        try {
            $tokenPath = storage_path('app/gmail-token.json');
            
            if (file_exists($tokenPath)) {
                unlink($tokenPath);
                Log::info('Gmail tokens cleared');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Gmail tokens cleared successfully',
                    'next_step' => 'Visit /gmail-auth-url to re-authorize'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'No tokens found to clear'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to clear tokens',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}