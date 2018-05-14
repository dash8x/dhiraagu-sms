<?php

namespace Dash8x\DhiraaguSms;

class DhiraaguSmsDelivery extends DhiraaguSmsResponse
{
    /**
     * The message id
     * @var string
     */
    public $message_id;

    /**
     * The message status id
     * @var string
     */
    public $message_status_id;

    /**
     * The message status desc
     * @var string
     */
    public $message_status_desc;

    /**
     * The recipient devices
     * @var DhiraaguSmsDevice[]
     */
    public $devices = [];

    /**
     * Dhiraagu Sms Message constructor.
     *
     * @param array $response
     */
    public function __construct($response = [])
    {
        parent::__construct($response);

        $this->message_id = $response['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['MESSAGE_ID'] ?? null;
        $this->message_status_id = $response['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['STATUS_ID'] ?? null;
        $this->message_status_desc = $response['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['STATUS_DESCRIPTION'] ?? null;

        $recipients = $response['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['RECIPIENT_STATUS'] ?? [];
        foreach ($recipients as $arr) {
            $device_data = $arr['DEVICE'] ?? [];
            if ($device_data) {
                $this->devices[] = new DhiraaguSmsDevice($device_data);
            }
        }
    }

    /**
     * Get device
     *
     * @param string $number
     * @return DhiraaguSmsDevice
     */
    public function getDevice($number)
    {
        foreach ($this->devices as $device) {
            if ($device->number == $number) {
                return $device;
            }
        }

        return null;
    }

}
