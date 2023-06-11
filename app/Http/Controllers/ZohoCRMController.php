<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Asciisd\Zoho\ZohoManager;

class ZohoCRMController extends Controller
{
    public function createDeal(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'dealName' => 'required|string',
            'dealStage' => 'required|string',
        ]);

        try {
            // Create a new deal using ZohoManager
            $deals = ZohoManager::useModule('Deals');

            $response = $deals->create([
                'Deal_Name' => $validatedData['dealName'],
                'Stage' => $validatedData['dealStage'],
            ]);

            // Check if the deal was created successfully
            if ($response->getStatusCode() === 201) {
                // Deal created successfully
                return response()->json(['message' => 'Deal created successfully'], 201);
            } else {
                // Deal creation failed
                $errorMessage = $response->getMessage();
                return response()->json(['error' => $errorMessage], 400);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json(['error' => 'An error occurred while creating the deal'], 500);
        }
    }

    public function createAccount(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'accountName' => 'required|string',
            'accountWebsite' => 'required|url',
            'accountPhone' => 'required|string',
        ]);

        try {
            // Create a new account using ZohoManager
            $accounts = ZohoManager::useModule('Accounts');

            $response = $accounts->create([
                'Account_Name' => $validatedData['accountName'],
                'Website' => $validatedData['accountWebsite'],
                'Phone' => $validatedData['accountPhone'],
            ]);

            // Check if the account was created successfully
            if ($response->getStatusCode() === 201) {
                // Account created successfully
                return response()->json(['message' => 'Account created successfully'], 201);
            } else {
                // Account creation failed
                $errorMessage = $response->getMessage();
                return response()->json(['error' => $errorMessage], 400);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            return response()->json(['error' => 'An error occurred while creating the account'], 500);
        }
    }

    public function createRecords(Request $request)
    {
        $dealResponse = $this->createDeal($request);
        $accountResponse = $this->createAccount($request);

        // Check if both the deal and account were created successfully
        if ($dealResponse->getStatusCode() === 201 && $accountResponse->getStatusCode() === 201) {
            // Records created successfully
            return response()->json(['message' => 'Records created successfully'], 201);
        } else {
            return response()->json(['dealResponse' => $dealResponse, 'accountResponse' => $accountResponse], 400);
        }
    }

    public function test() {
        $leads = ZohoManager::useModule('Leads');
        $response = $leads->create([
            'First_Name' => 'Amr',
            'Last_Name' => 'Emad',
            'Email' => 'test@asciisd.com',
            'Phone' => '012345678910',
        ]);

        return $response;
    }
}
