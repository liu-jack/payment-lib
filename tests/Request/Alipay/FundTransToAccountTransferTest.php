<?php
namespace Archman\PaymentLib\Test\Request\Alipay;

use PHPUnit\Framework\TestCase;
use Archman\PaymentLib\Test\Config;
use Archman\PaymentLib\Test\Config\AlipayConfig;
use Archman\PaymentLib\Request\Alipay\FundTransToAccountTransfer;
use Archman\PaymentLib\Request\Alipay\FundTransOrderQuery;

class FundTransToAccountTransferTest extends TestCase
{
    public function testMakingTransferParameters()
    {
        $cases = Config::get('alipay', 'testCases', 'request', 'FundTransToAccountTransfer');
        foreach ($cases as $each) {
            $configData = Config::get('alipay', 'config', $each['appID']);
            $config = new AlipayConfig($configData);
            $config->setOpenAPIDefaultSignType($each['signType']);

            $request = (new FundTransToAccountTransfer($config))
                ->setAmount($each['fields']['amount'])
                ->setPayeeAccount($each['fields']['payee_account'])
                ->setPayeeRealName($each['fields']['payee_real_name'])
                ->setOutBizNo($each['fields']['out_biz_no'])
                ->setPayeeType($each['fields']['payee_type'])
                ->setTimestamp(new \DateTime($each['fields']['timestamp']))
                ->setRemark($each['fields']['remark'] ?? null)
                ->setPayerShowName($each['fields']['payer_show_name'] ?? null);

            $this->assertEquals($each['parameters'], $request->makeParameters());
        }
    }
}