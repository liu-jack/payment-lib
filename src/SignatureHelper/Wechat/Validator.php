<?php
namespace Archman\PaymentLib\SignatureHelper\Wechat;

use Archman\PaymentLib\ConfigManager\WechatConfigInterface;
use Archman\PaymentLib\Exception\SignatureException;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
 */
class Validator
{
    use SignStringPackerTrait;

    private $config;

    public function __construct(WechatConfigInterface $config)
    {
        $this->config = $config;
    }

    public function validate(string $signature, string $sign_type, array $data, array $exclude = [])
    {
        $packed_string = $this->packRequestSignString($data, $exclude);

        switch (strtoupper($sign_type)) {
            case 'MD5':
                $result = $this->validateSignMD5($signature, $packed_string);
                break;
            case 'HMAC-SHA256':
                $result = $this->validateSignSHA256($signature, $packed_string);
                break;
            default:
                throw new SignatureException("Unsupported Wechat Sign Type: {$sign_type}");
        }

        if (!$result) {
            throw new SignatureException('Failed To Validate Wechat Signature.');
        }
    }

    private function validateSignMD5(string $signature, string $packed_string): bool
    {
        $secret_key = $this->config->getApiKey();

        return strtoupper(md5("{$packed_string}&key={$secret_key}")) === $signature;
    }

    private function validateSignSHA256(string $signature, string $packed_string): bool
    {
        $secret_key = $this->config->getApiKey();

        return strtoupper(hash_hmac('sha256', "{$packed_string}&key={$secret_key}", $secret_key)) === $signature;
    }
}