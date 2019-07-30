# 環境要求
- PHP 5.5 以上
- CURL

# 一、登入操作方式

```php
$config = [
  'clientId' => '', //Channel ID
  'clientSecret' => '', //Channel secret 
  'redirectUri' => '' //回傳位置
];
$lineLogin = new Linelogin($config);
```

利用以下方式呼叫網址
```php
$lineLogin->getAuthorizeUri();
```

假設回傳網址預設為 https://xxx.xxx.xxx

之後會變成 https://xxx.xxx.xxx?code=___&state=___

- code : 帶入getAccessToken()驗證
- state : 用來分辨使用位置

```php
$lineLogin->getAccessToken($code);
```
*得到的access_token可以存活達30天!!

如果要登出，使用revokeAccessToken()

```php
$lineLogin->revokeAccessToken($code);
```

# 二、Line message 使用

如果有需要驗證的話，將以下參數帶入在驗證
```php
$config = [
  'clientId' => '', //Channel ID
  'clientSecret' => '', //Channel secret 
  'channelAccessToken' => '' //
];
$lineLogin = new Linebot($config);
```

- messageReply() : 回覆
- messagePush() : 單一性發佈
- messageMultCast() : 一次性多人發佈

依照個人需求使用，另外關於回覆的格式與方式

可以參照官方的說明或是爬文找找其他方式。
