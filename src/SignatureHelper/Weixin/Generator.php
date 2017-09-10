<?php
namespace Utils\PaymentVendor\SignatureHelper\Weixin;

use Exception\UnsupportedVendorSignTypeException;
use Utils\PaymentVendor\ConfigManager\WeixinConfig;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Generator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WeixinConfig $config)
    {
        $this->config = $config;
    }

    public function makeSign(array $data, string $sign_type): string
    {
        $sign_type = strtoupper($sign_type);
        $packed_string = $this->packRequestSignString($data);

        switch ($sign_type) {
            case 'MD5':
                $sign = $this->makeSignMD5($packed_string);
                break;
            case 'HMAC-SHA256':
                $sign = $this->makeSignSHA256($packed_string);
                break;
            default:
                throw new UnsupportedVendorSignTypeException();
        }

        return $sign;
    }

    private function makeSignMD5(string $packed_string): string
    {
        $secret_key = $this->config->getSecretKey();

        return strtoupper(md5("{$packed_string}&key={$secret_key}"));
    }

    private function makeSignSHA256(string $packed_string): string
    {
        $secret_key = $this->config->getSecretKey();

        return strtoupper(hash_hmac('sha256', "{$packed_string}&key={$secret_key}", $secret_key));
    }
}