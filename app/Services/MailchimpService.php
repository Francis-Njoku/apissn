<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class MailchimpService
{
    protected $client;

    public function __construct()
    {
        $this->client = new ApiClient();
        $this->client->setConfig([
            'apiKey' => env('MAILCHIMP_APIKEY'),
            'server' => env('MAILCHIMP_SERVER'),
        ]);
        $apiKey = env('MAILCHIMP_APIKEY');
        $server = env('MAILCHIMP_SERVER');
        if (!$apiKey || !$server) {
            throw new \Exception("Mailchimp API key or server is not set in the .env file");
        }
    }

    private function getSubscriberHash($email)
    {
        return md5(strtolower($email));
    }

    public function subscribeToList($listId, $email, $mergeFields = [])
    {
        return $this->client->lists->addListMember($listId, [
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => $mergeFields,
        ]);
    }

    public function subscribeOrUpdateList($listId, $email, $mergeFields = [])
    {
        $subscriberHash = $this->getSubscriberHash($email);
        $data = [
            'email_address' => $email,
            'status_if_new' => 'subscribed',
        ];

        if (!empty($mergeFields)) {
            $data['merge_fields'] = $mergeFields;
        }

        try {
            // Debug statements
            \Log::info('List ID: ' . $listId);
            \Log::info('Subscriber Hash: ' . $subscriberHash);

            // Attempt to get the list member
            $response = $this->client->lists->getListMember($listId, $subscriberHash);
            // If the member exists, update their information
            return $this->client->lists->updateListMember($listId, $subscriberHash, $data);
        } catch (ApiException $e) {
            if ($e->getCode() === 404) {
                // Subscriber not found, add new subscriber
                return $this->client->lists->addListMember($listId, $data);
            }

            throw $e; // Rethrow exception if it's not a 404
        }
    }

    public function unsubscribeFromList($listId, $email)
    {
        $subscriberHash = $this->client->subscriberHash($email);

        try {
            return $this->client->lists->updateListMember($listId, $subscriberHash, [
                'status' => 'unsubscribed',
            ]);
        } catch (ApiException $e) {
            throw new \Exception("Unable to unsubscribe the user: " . $e->getMessage());
        }
    }

    // Add more methods as needed
}
