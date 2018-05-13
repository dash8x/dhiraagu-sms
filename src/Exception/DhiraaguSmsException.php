<?php

namespace Dash8x\DhiraaguSms\Execption;

class DhiraaguSmsException extends \Exception
{
    /**
     * @param string $error_code
     * @param string $error_desc
     * @return static
     */
    public static function messageFailed($error_code = '0', $error_desc = 'Unknown Error')
    {
        return new static("SMS was not sent. $error_code: $error_desc");
    }
}
