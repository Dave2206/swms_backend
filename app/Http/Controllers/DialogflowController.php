<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Dialogflow\V2\Client\SessionsClient;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\DetectIntentRequest;

class DialogflowController extends Controller
{
    public function chat(Request $request)
    {
        \Log::debug(__METHOD__);

        // Get user input
        $userMessage = $request->input('message');
        \Log::info('User Input:', ['message' => $userMessage]);

        // Check if message is empty
        if (empty($userMessage)) {
            return response()->json(['error' => 'Text input is required'], 400);
        }

        // Get project ID and Dialogflow credentials
        $projectId = env('DIALOGFLOW_PROJECT_ID');
        $keyFilePath = storage_path('app/dialogflow-key.json');

        // Ensure credentials file exists
        if (!file_exists($keyFilePath)) {
            return response()->json(['error' => 'Dialogflow key file not found'], 500);
        }

        // Initialize Dialogflow Session
        $sessionsClient = new SessionsClient([
            'credentials' => $keyFilePath
        ]);
        $session = $sessionsClient->sessionName($projectId, uniqid());

        // Create Text Input
        $textInput = new TextInput();
        $textInput->setText($userMessage);
        $textInput->setLanguageCode('en');

        // Create Query Input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        // Create DetectIntentRequest ✅ (Proper way)
        $detectIntentRequest = new DetectIntentRequest();
        $detectIntentRequest->setSession($session);
        $detectIntentRequest->setQueryInput($queryInput);

        // Detect Intent using the correctly structured request ✅
        $response = $sessionsClient->detectIntent($detectIntentRequest);
        $result = $response->getQueryResult();

        // Close Session Client
        $sessionsClient->close();

        // Return the chatbot's reply
        return response()->json(['reply' => $result->getFulfillmentText()]);
    }
}
