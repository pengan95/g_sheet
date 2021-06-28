<?php
declare(strict_types=1);

namespace MeiKaiGsuit\GSheet;

use Google_Service_Sheets;
use Google_Service_Drive;

class GClient
{
    const SHEET_READ_SCOPE_GROUP = 'sheet_r';
    const SHEET_WRITE_SCOPE_GROUP = 'sheet_rw';

    const CLIENT_AUTH_PROMPT_NONE = 'none';
    const CLIENT_AUTH_PROMPT_AGREE_SCREEN = 'select_account consent';

    public $scopes = [];
    public $applicationName = 'MeiKai Service';

    private $scopeGroups = [
        self::SHEET_READ_SCOPE_GROUP => [
            Google_Service_Sheets::SPREADSHEETS_READONLY,
            Google_Service_Drive::DRIVE_METADATA
        ],
        self::SHEET_WRITE_SCOPE_GROUP => [
            Google_Service_Sheets::SPREADSHEETS,
//            Google_Service_Drive::DRIVE_FILE,
//            Google_Service_Drive::DRIVE_METADATA
        ],
    ];

    private $configDir;

    /**
     * 初始化
     * @param array|string $scopes
     */
    public function __construct($scopes)
    {
        if (is_array($scopes)) {
            $this->scopes = $scopes;
        } else {
            $this->scopes = $this->getDefinedScopeGroup($scopes);
        }

        if (defined('APP_CONFIG_PATH')) {
            $this->configDir = APP_CONFIG_PATH;
        } else {
            $this->configDir = dirname(dirname(__DIR__));
        }
    }

    public function getDefinedScopeGroup($scope_type): array
    {
        if (isset($this->scopeGroups[$scope_type])) {
            return $this->scopeGroups[$scope_type];
        }
        throw new \InvalidArgumentException("scope type [{$scope_type}] not defined");
    }

    /**
     * 检查客户端认证文件是否存在
     * */
    public function getClientCredential() : string
    {
        $file_name = $this->configDir . "/client_credential.json";
        if (file_exists($file_name)) {
            return $file_name;
        }
        throw new \InvalidArgumentException('no client credential file in config dir ' . $file_name);
    }

    public function setAccessToken($access_token)
    {
        $file_name = $this->configDir . "/token.json";
        file_put_contents($file_name, json_encode($access_token));
    }

    public function getAccessToken()
    {
        $file_name = $this->configDir . "/token.json";
        if (file_exists($file_name)) {
            return json_decode(file_get_contents($file_name), true);
        }
        return null;
    }

    public function getClient() : \Google_Client
    {
        $client = new \Google_Client();

        //初始化客户端参数
        $client->setApplicationName($this->applicationName);
        $client->setScopes($this->scopes);
        $client->setAuthConfig($this->getClientCredential());
        $client->setAccessType('offline');
        $client->setPrompt(self::CLIENT_AUTH_PROMPT_AGREE_SCREEN);

        $refresh_token = '';
        //获取保存的 Access Token
        if ($accessToken = $this->getAccessToken()) {
            //refresh token 不保存在 Google_Client->token 属性内，需要单独保存
            $refresh_token = $accessToken['refresh_token'];
            $client->setAccessToken($accessToken);
        }

        if (!$client->isAccessTokenExpired()){
            return  $client;
        }


        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            $refresh_token = $client->getRefreshToken();

            if (array_key_exists('error', $accessToken)) {
                throw new \Exception(join(', ', $accessToken));
            }
        }

        $this->setAccessToken(array_merge(
            $client->getAccessToken(), ['refresh_token' => $refresh_token]
        ));

        return $client;
    }
}