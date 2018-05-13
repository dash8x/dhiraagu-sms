<?php

namespace Dash8x\DhiraaguSms;

use Dash8x\DhiraaguSms\Exception\DhiraaguSmsException;
use Exception;

class DhiraaguSms
{
    /**
     * Default API url
     * @var string
     */
    const DEFAULT_API_URL = 'https://bulkmessage.dhiraagu.com.mv/partners/xmlMessage.jsp';

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
     * @return array
     * @throws DhiraaguSmsException
     */
    public function send($mobiles, $message)
    {
        // build the numbers xml
        if(! is_array($mobiles)) {
            $mobiles = [$mobiles];
        }

        $numbers_xml = '';
        foreach ($mobiles as $number) {
            $numbers_xml .= static::renderXML('_number', compact('number'));
        }

        $data = [
            'username' => $this->username,
            'password' => $this->password,
            'numbers' => $numbers_xml,
            'message' => $message
        ];

        $xml = static::renderXML('send', $data);

        $response = $this->sendRequest($xml);

        // disable warnings
        libxml_use_internal_errors(true);

        $xmlresp = simplexml_load_string($response);
        $arr = json_decode(json_encode($xmlresp), 1);

        // re-enable warnings
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $msg_id = $arr['TELEMESSAGE_CONTENT']['RESPONSE']['MESSAGE_ID'];
        $msg_key = $arr['TELEMESSAGE_CONTENT']['RESPONSE']['MESSAGE_KEY'];
        $msg_status = $arr['TELEMESSAGE_CONTENT']['RESPONSE']['RESPONSE_STATUS'];
        $msg_status_des = $arr['TELEMESSAGE_CONTENT']['RESPONSE']['RESPONSE_STATUS_DESC'];

        if ($msg_status != '100') {
            throw DhiraaguSmsException::messageFailed($msg_status, $msg_status_des);
        }

        $ret = [
            'message_id'        => $msg_id,
            'message_key'       => $msg_key,
            'message_status'    => $msg_status,
            'status_desc'       => $msg_status_des,
            'message'           => $message,
            'numbers'           => $mobiles,
            'message_sent_at'   => date('Y-m-d h:i:sa'),
        ];

        return $ret;
    }

    /**
     * Checks the delivery status of a message
     *
     * @param string $msg_id
     * @param string $msg_key
     * @return array
     */
    public function delivery($msg_id, $msg_key)
    {
        $data = [
            'message_id' => $msg_id,
            'message_key' => $msg_key
        ];

        $xml = static::renderXML('delivery', $data);

        $response = $this->sendRequest($xml);

        $xmlresp = simplexml_load_string($response);

        $arr = json_decode(json_encode($xmlresp), 1);

        $status_id = $arr['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['STATUS_ID'];
        $status = $arr['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['RECIPIENT_STATUS']['DEVICE']['STATUS'];
        $delivered_date = $arr['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['RECIPIENT_STATUS']['DEVICE']['STATUS_DATE'];
        $number = $arr['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['RECIPIENT_STATUS']['DEVICE']['VALUE'];
        $delivery_status_desc = $arr['TELEMESSAGE_CONTENT']['MESSAGE_STATUS']['RECIPIENT_STATUS']['DEVICE']['DESCRIPTION'];

        $ret = [
            'status_id' => $status_id,
            'status' => $status,
            'status_desc' => $delivery_status_desc,
            'number' => $number,
            'delivered_at' => $delivered_date
        ];

        return $ret;
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
