<?php

namespace Archman\PaymentLib\Request\WeChat;

use Archman\PaymentLib\ConfigManager\WeChatConfigInterface;
use Archman\PaymentLib\Request\ParameterHelper;
use Archman\PaymentLib\Request\ParameterMakerInterface;
use Archman\PaymentLib\Request\RequestableInterface;
use Archman\PaymentLib\Request\WeChat\Traits\NonceStrTrait;
use Archman\PaymentLib\Request\WeChat\Traits\RequestPreparationTrait;
use Archman\PaymentLib\Request\WeChat\Traits\ResponseHandlerTrait;
use Archman\PaymentLib\SignatureHelper\WeChat\Generator;

/**
 * 转换短链接.
 *
 * @see https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_9
 */
class ShortURL implements RequestableInterface, ParameterMakerInterface
{
    use NonceStrTrait;
    use RequestPreparationTrait;
    use ResponseHandlerTrait;

    private const URI = 'https://api.mch.weixin.qq.com/tools/shorturl';

    private $config;

    private $signType;

    private $params = [
        'long_url' => null,
    ];

    public function __construct(WeChatConfigInterface $config)
    {
        $this->config = $config;
        $this->signType = $config->getDefaultSignType();
    }

    public function makeParameters(): array
    {
        ParameterHelper::checkRequired($this->params, ['long_url']);

        $parameters['appid'] = $this->config->getAppID();
        $parameters['mch_id'] = $this->config->getMerchantID();
        $parameters['nonce_str'] = $this->getNonceStr();
        $parameters['sign_type'] = $this->signType;
        $parameters['sign'] = (new Generator($this->config))->makeSign($parameters, $this->signType);

        return $parameters;
    }

    public function setLongURL(string $url): self
    {
        $this->params['long_url'] = $url;

        return $this;
    }
}