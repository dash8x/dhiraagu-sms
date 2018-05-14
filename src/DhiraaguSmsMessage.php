<?php

namespace Dash8x\DhiraaguSms;

class DhiraaguSmsMessage extends DhiraaguSmsResponse
{
    /**
     * The message id
     * @var string
     */
    public $message_id;

    /**
     * The message key
     * @var string
     */
    public $message_key;

    /**
     * The date the message was sent
     * @var string
     */
    public $sent_date;

    /**
     * The numbers that the message was sent to
     * @var array
     */
    public $numbers = [];

    /**
     * The message
     * @var string
     */
    public $message;

    /**
     * Dhiraagu Sms Message constructor.
     *
     * @param string $message
     * @param array $numbers
     * @param array $response
     * @internal param string $username
     * @internal param string $password
     * @internal param string $url
     */
    public function __construct($message, $numbers, $response = [])
    {
        parent::__construct($response);

        $this->message_id = $response['TELEMESSAGE_CONTENT']['RESPONSE']['MESSAGE_ID'] ?? null;
        $this->message_key = $response['TELEMESSAGE_CONTENT']['RESPONSE']['MESSAGE_KEY'] ?? null;

        $this->message = $message;
        $this->numbers = $numbers;
        $this->sent_date = date('Y-m-d h:i:sa');
    }

}
