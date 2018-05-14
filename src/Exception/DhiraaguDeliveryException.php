<?php

namespace Dash8x\DhiraaguSms\Exception;

class DhiraaguDeliveryException extends \Exception
{
    /**
     * @param string $error_code
     * @param string $error_desc
     * @return static
     */
    public static function messageFailed($error_code = '0', $error_desc = 'Unknown Error')
    {
        return new static("Delivery status could not be checked. $error_code: $error_desc");
    }
}
