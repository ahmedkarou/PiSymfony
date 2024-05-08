<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twilio\Rest\Client;

class SmsService
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sendSms(string $to, string $message): void
    {
        $twilioAccountSid = $this->container->getParameter('twilio_account_sid');
        $twilioAuthToken = $this->container->getParameter('twilio_auth_token');
        $twilioPhoneNumber = $this->container->getParameter('twilio_phone_number');

        $twilioClient = new Client($twilioAccountSid, $twilioAuthToken);

        $twilioClient->messages->create(
            $to,
            [
                'from' => $twilioPhoneNumber,
                'body' => $message
            ]
        );
    }
}
