<?php
/**
 *
 */
class Linelogin
{
    const AUTHORIZE_URI = 'https://access.line.me/oauth2/v2.1/authorize';
    const ACCESS_TOKEN_URI = 'https://api.line.me/oauth2/v2.1/token';
    const VERIFY_ACCESS_TOKEN_URI = 'https://api.line.me/oauth2/v2.1/verify';
    const REVOKE_URI = 'https://api.line.me/oauth2/v2.1/revoke';
    const PROFILE_URI = 'https://api.line.me/v2/profile';
    const STATUS_URI = 'https://api.line.me/friendship/v1/status';

    private $clientId;
    private $clientSecret;
    private $redirectUri;
    private $sslCheck = false;
    private $lang = 'en';

    /**
     * Line Login Construct
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
      * Line Authorize Uri (GET)
      *
      * @param string $state
      */
    public function getAuthorizeUri($state)
    {
        $parameter = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'state' => $state,
        ];
        return self::AUTHORIZE_URI.'?'.http_build_query($parameter).
        '&redirect_uri='.$this->redirectUri.'&scope=openid%20profile';
    }

    /**
     * Issue access token (POST)
     *
     * @param string $code
     */
    public function getAccessToken($code)
    {
        $post = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ACCESS_TOKEN_URI);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != '200') {
            return [
                'status' => 'error',
                'message' => $this->getHttpStatusDescription($info['http_code'])
            ];
        }

        $result = json_decode($result, true);

        if ($this->verifyAccessToken($result['access_token']) === true) {
            return [
                'status' => 'success',
                'date' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Verification Failed.'
            ];
        }
    }

    /**
     * Verify access token (GET)
     *
     * @param string $accessToken
     */
    private function verifyAccessToken($accessToken)
    {
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , self::VERIFY_ACCESS_TOKEN_URI.'?access_token='.$accessToken);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);
        return ($result['client_id'] != $this->clientId) ? false : true;
    }

    /**
     * Refresh access token (POST)
     *
     * @param string $refreshToken
     */
    public function refreshAccessToken($refreshToken)
    {
        $post = [
            'grant_type' => $refreshToken,
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::ACCESS_TOKEN_URI);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != '200') {
            return [
                'status' => 'error',
                'message' => $this->getHttpStatusDescription($info['http_code'])
            ];
        }

        $result = json_decode($result, true);

        if ($this->verifyAccessToken($result['access_token']) === true) {
            return [
                'status' => 'success',
                'date' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Verification Failed'
            ];
        }
    }

    /**
     * Refresh access token
     *
     * @param string $accessToken (POST)
     */
    public function revokeAccessToken($accessToken)
    {
        $post = [
            'access_token' => $accessToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::REVOKE_URI);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != '200') {
            return [
                'status' => 'error',
                'message' => $this->getHttpStatusDescription($info['http_code'])
            ];
        }
        return [
            'status' => 'success'
        ];
    }

    /**
     * Get user profile (GET)
     *
     * @param string $accessToken
     */
    public function getUserProfile($accessToken, $key = '')
    {
        $header = 'Authorization:Bearer '.$accessToken;
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , self::PROFILE_URI);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$header]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if ($key != '') {
            return isset($result[$key]) ? $result[$key] : '';
        } else {
            return $result;
        }
    }

    /**
     * Get friendship status (GET)
     *
     * @param string $accessToken
     */
    public function getFriendShipStatus()
    {
        $header = 'Authorization:Bearer '.$accessToken;
        $ch = curl_init();
        curl_setopt($ch , CURLOPT_URL , self::STATUS_URI);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$header]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslCheck);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslCheck);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    /**
     * Get Http status Description
     *
     * @param string $status
     */
    private function getHttpStatusDescription($status)
    {
        $statusArr = [
            'tw' => [
                '400' => '請求出現問題，請檢查參數和JSON格式。',
                '401' => '檢查授權header是否正確。',
                '403' => '未授權使用API，請確認是否有取得API授權。',
                '429' => '請求次數過多。',
                '500' => 'API服務端出現錯誤。',
            ],
            'en' => [
                '400' => 'Problem with the request. Check the request parameters and JSON format.',
                '401' => 'Check that the authorization header is correct.',
                '403' => 'Not authorized to use the API. Confirm that your account or plan is authorized to use the API.',
                '429' => 'Make sure that you are within the rate limits for requests.',
                '500' => 'Temporary error on the API server.',
            ]
        ];
        return isset($statusArr[$this->lang][$status]) ? $statusArr[$this->lang][$status] : 'Http Status Code is not exist.';
    }
}
