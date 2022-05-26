<?php

namespace Dash8x\DhiraaguSms;

use Dash8x\DhiraaguSms\Exception\DhiraaguDeliveryException;
use Dash8x\DhiraaguSms\Exception\DhiraaguSmsException;
use Exception;

class DhiraaguSms
{
    /**
     * Default API url
     * @var string
     */
    const DEFAULT_API_URL = 'https://bulksms.dhiraagu.com.mv/partners/xmlMessage.jsp';

    /**
     * The bulk sms username
     * @var string
     */
    private $username;

    /**
     * The bulk sms password
     * @var string
     */
    private $password;

    /**
     * The bulk sms API url
     * @var string
     */
    protected $url;

    /**
     * Request xml
     * @var array
     *
     */
    protected static $xml = [];

    /**
     * Dhiraagu Sms constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $url
     */
    public function __construct($username, $password, $url = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->url = $url ?: self::DEFAULT_API_URL;
    }

    /**
     * Escape xml content
     *     e
     * @param string $text
     * @return string
     */
    protected static function escapeXML($text)
    {
        return htmlspecialchars($text, ENT_XML1, 'UTF-8');
    }

    /**
     * Loads the xml file, substitutes params
     *
     * @param string $file
     * @param array $params
     * @return string
     */
    protected static function renderXML($file, $params)
    {
        // check if already loaded
        if (empty(static::$xml[$file])) {
            // load if file not loaded
            static::$xml[$file] = file_get_contents(__DIR__ . '/xml/'.$file.'.xml');
        }

        // build the search and replace arrays
        $search = [];
        $replace = [];

        foreach ($params as $key => $value) {
            $search[] = ':'.$key;
            $replace[] = $value;
        }

        // return the rendered file
        return str_replace($search, $replace, static::$xml[$file]);
    }

    /**
     * Sends a message
     *
     * @param string|array $mobiles
     * @param string $message
     * @return DhiraaguSmsMessage
     * @throws DhiraaguSmsException
     */
    public function send($mobiles, $message)
    {
        // build the numbers xml
        if (! is_array($mobiles)) {
            $mobiles = [$mobiles];
        }

        $numbers_xml = '';
        foreach ($mobiles as $number) {
            $number = static::escapeXML($number);

            $numbers_xml .= static::renderXML('_number', compact('number'));
        }

        $data = [
            'username' => static::escapeXML($this->username),
            'password' => static::escapeXML($this->password),
            'numbers' => $numbers_xml,
            'message' => static::escapeXML($message),
        ];

        $xml = static::renderXML('send', $data);

        $response = $this->sendRequest($xml);

        // disable warnings
        libxml_use_internal_errors(true);

        $xmlresp = simplexml_load_string($response);
        $arr = json_decode(json_encode($xmlresp), true);

        // re-enable warnings
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $message = new DhiraaguSmsMessage($message, $mobiles, $arr);

        if ($message->response_status != '100') {
            throw DhiraaguSmsException::messageFailed($message->response_status, $message->response_status_desc);
        }

        return $message;
    }

    /**
     * Checks the delivery status of a message
     *
     * @param string $msg_id
     * @param string $msg_key
     * @return DhiraaguSmsDelivery
     * @throws DhiraaguDeliveryException
     */
    public function delivery($msg_id, $msg_key)
    {
        $data = [
            'message_id' => static::escapeXML($msg_id),
            'message_key' => static::escapeXML($msg_key)
        ];

        $xml = static::renderXML('delivery', $data);

        $response = $this->sendRequest($xml);

        // disable warnings
        libxml_use_internal_errors(true);

        $xmlresp = simplexml_load_string($response);
        $arr = json_decode(json_encode($xmlresp), true);

        // re-enable warnings
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $delivery = new DhiraaguSmsDelivery($arr);

        if (empty($delivery->message_status_id)) {
            throw DhiraaguDeliveryException::messageFailed($delivery->response_status, $delivery->response_status_desc);
        }

        return $delivery;
    }

    /**
     * Sends the request
     *
     * @param string $xml
     * @return string|mixed
     * @throws Exception
     */
    private function sendRequest($xml)
    {
        $url = $this->url;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type:application/xml"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        if (false === $resp) {
            throw new Exception(curl_error($curl) . ' ' . curl_errno($curl), curl_errno($curl));
        }

        curl_close($curl);

        return $resp;
    }

}
