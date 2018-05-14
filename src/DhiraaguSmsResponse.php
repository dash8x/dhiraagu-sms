<?php

namespace Dash8x\DhiraaguSms;

abstract class DhiraaguSmsResponse
{
    /**
     * The response status
     * @var string
     */
    public $response_status;

    /**
     * The response status description
     * @var string
     */
    public $response_status_desc;

    /**
     * Dhiraagu Sms Response constructor.
     *
     * @param array $response
     */
    public function __construct($response = [])
    {
        $this->response_status = $response['TELEMESSAGE_CONTENT']['RESPONSE']['RESPONSE_STATUS'] ?? null;
        $this->response_status_desc = $response['TELEMESSAGE_CONTENT']['RESPONSE']['RESPONSE_STATUS_DESC'] ?? null;
    }
}
