<?php

namespace Dash8x\DhiraaguSms;

class DhiraaguSmsDevice
{
    /**
     * The device type
     * @var string
     */
    public $type;

    /**
     * The device number
     * @var string
     */
    public $number;

    /**
     * The device status
     * @var string
     */
    public $status;

    /**
     * The device status description
     * @var string
     */
    public $status_desc;

    /**
     * The device status date
     * @var string
     */
    public $status_date;

    /**
     * Dhiraagu Sms Device constructor.
     *
     * @param array $response
     */
    public function __construct($response = [])
    {
        $this->type = $response['TYPE'] ?? null;
        $this->status = $response['STATUS'] ?? null;
        $this->status_desc = $response['DESCRIPTION'] ?? null;
        $this->status_date = $response['STATUS_DATE'] ?? null;

        $number = $response['VALUE'] ?? null;
        if ($number) {
            $number = preg_replace('/[^0-9]/', '', $number);
        }

        $this->number = $number;
    }

}
