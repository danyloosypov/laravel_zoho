<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{
    public function createDealAndAccount(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'deal_name' => 'required|string',
            'deal_stage' => 'required|string',
            'account_name' => 'required|string',
            'account_website' => 'nullable|string',
            'account_phone' => 'nullable|string',
        ]);

        // Prepare the request payload for creating the account
        $accountPayload = [
            'Account_Name' => $validatedData['account_name'],
            'Website' => $validatedData['account_website'],
            'Phone' => $validatedData['account_phone'],
            // Add more account fields as needed
        ];

        // Make the API request to create the account
        $accountUrl = 'https://www.zohoapis.com/crm/v2/Accounts';
        $accountResponse = $this->makeZohoApiRequest($accountUrl, $accountPayload);

        // Get the newly created account's ID
        // Prepare the request payload for creating the deal
        $dealPayload = [
            'Deal_Name' => $validatedData['deal_name'],
            'Stage' => $validatedData['deal_stage'],
        ];

        // Make the API request to create the deal
        $dealUrl = 'https://www.zohoapis.com/crm/v2/Deals';
        $dealResponse = $this->makeZohoApiRequest($dealUrl, $dealPayload);

        // Check if the deal and account creation was successful
        if ($dealResponse && $accountResponse) {
            return response()->json([
                'dealResponse' => $dealResponse,
                'accountResponse' => $accountResponse,
            ]);
        } else {
            // Handle the error response if needed
            $error = $dealResponse ?? $accountResponse;
            return response()->json([
                'success' => false,
                'message' => 'Failed to create deal and account.',
                'error' => $error,
            ], 500);
        }
    }

    public function testCreateDealAndAccount()
    {
        $payload = '{
            "deal_name": "Test Deal",
            "deal_stage": "Prospecting",
            "account_name": "Test Account",
            "account_website": "https://www.example.com",
            "account_phone": "+1 123-456-7890"
        }';

        $request = new Request(json_decode($payload, true));
        return $this->createDealAndAccount($request);
    }

    private function makeZohoApiRequest($url, $payload)
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['data' => [$payload]]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Zoho-oauthtoken ' . $this->getAccessToken(),
                'Content-Type: application/json',
            ],
            CURLOPT_CAINFO => public_path('cacert.pem'), // Path to the cacert.pem file
        ]);

        // Execute the cURL request
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception('cURL Error: ' . $error);
        }

        // Close cURL and decode the response JSON
        curl_close($curl);
        $responseData = json_decode($response, true);

        // Check for Zoho API errors
        if (isset($responseData['data']) && empty($responseData['data'])) {
            throw new \Exception('Zoho API Error: ' . $responseData['message']);
        }

        return $responseData;
    }

    public function getAccessToken()
    {
        $curl = curl_init();

        // Set the request URL and method
        curl_setopt($curl, CURLOPT_URL, 'https://accounts.zoho.eu/oauth/v2/token');
        curl_setopt($curl, CURLOPT_POST, true);

        // Prepare the request payload
        $payload = [
            'refresh_token' => "1000.cf65f52b999f4563fef08ea029276ada.e89b490143a3e72b97e60691364c9c60",
            'client_id' => "1000.M3281YLPTG4MHFW3IYOHWBAM5KWJRK",
            'client_secret' => "cb4472b4f68acc9a0b3f147f1bec10d6bc64fcc4b6",
            'redirect_uri' => "http://localhost:8000",
            'grant_type' => "refresh_token",
        ];

        // Set the request payload
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));

        // Set cURL options
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // Execute the request
        $response = curl_exec($curl);

        // Check if the request was successful
        if ($response !== false) {
            $responseData = json_decode($response, true);
            return $responseData['access_token'];
        } else {
            return null;
        }
    }


}
