<?php

/**
 *
 */
class Linebot
{
    const ACCESS_TOKEN_URI = 'https://api.line.me/v2/oauth/accessToken';
    const REPLY_URI = 'https://api.line.me/v2/bot/message/reply';
    const PUSH_URI = 'https://api.line.me/v2/bot/message/push';
    const MULTICAST_URI = 'https://api.line.me/v2/bot/message/multicast';

    private $sslCheck = false;
    private $channelId;
    private $channelSecret;
    private $channelAccessToken;

    /**
     * Line Bot Construct
     *
     * @param array $config
     */
    function __construct($config = [])
    {
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * Issue channel access token (POST)
     *
     * @param array $postData
     */
    public function getAccessToken()
    {
        $postData = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->channelId,
            'client_secret' => $this->channelSecret
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::REPLY_URI);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);

        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Send reply message (POST)
     *
     * @param array $postData
     */
    public function messageReply($postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::REPLY_URI);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken
        ));

        curl_exec($ch);
        curl_close($ch);
    }

    /**
     * Send push message
     *
     * @param array $postData
     */
    public function messagePush($postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::PUSH_URI);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken
        ));

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return [$result, $info];
    }

    /**
     * Send multicast message
     *
     * @param array $postData
     */
    public function messageMultCast($postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::MULTICAST_URI);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->channelAccessToken
        ));

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return [$result, $info];
    }
}
