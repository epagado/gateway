<?php 
namespace Epagado\Gateway;

use PHPUnit\Framework\TestCase;

final class GatewayTest extends TestCase
{
    
    public function testInstance()
    {
        $tpv = new Gateway(require (__DIR__.'/../config.php'));

        $this->assertInstanceOf('Epagado\Gateway\Gateway', $tpv);

        return $tpv;
    }

    /**
     * @depends testInstance
     */
    public function testAmounts($tpv)
    {
        $this->assertEquals('000', $tpv->getAmount(0));
        $this->assertEquals('000', $tpv->getAmount(null));
        $this->assertEquals('400', $tpv->getAmount(4));
        $this->assertEquals('410', $tpv->getAmount(4.1));
        $this->assertEquals('410', $tpv->getAmount(4.10));
        $this->assertEquals('410', $tpv->getAmount(4.100));
        $this->assertEquals('410', $tpv->getAmount('4,10'));
        $this->assertEquals('410', $tpv->getAmount('4.10'));
        $this->assertEquals('410', $tpv->getAmount('4.1'));
        $this->assertEquals('410', $tpv->getAmount('4,1'));
        $this->assertEquals('499', $tpv->getAmount('4,99'));
        $this->assertEquals('499', $tpv->getAmount('4.99'));
        $this->assertEquals('499', $tpv->getAmount('4.999'));
        $this->assertEquals('499', $tpv->getAmount('4,999'));
        $this->assertEquals('040', $tpv->getAmount(0.4));
        $this->assertEquals('004', $tpv->getAmount(0.04));
        $this->assertEquals('000', $tpv->getAmount(0.004));
        $this->assertEquals('000', $tpv->getAmount(0.009));
        $this->assertEquals('400', $tpv->getAmount('4€'));
        $this->assertEquals('589', $tpv->getAmount('5,89€'));
        $this->assertEquals('589', $tpv->getAmount('$5.89'));

        $this->assertEquals('100050', $tpv->getAmount('1000,50'));
        $this->assertEquals('100050', $tpv->getAmount('1.000,50'));
        $this->assertEquals('100050', $tpv->getAmount('1,000.50'));
        $this->assertEquals('999999', $tpv->getAmount('9999,9999'));
    }

     /**
     * @depends testInstance
     */
    public function testOrderException($tpv)
    {
        $this->expectException(InvalidOrderException::class);
        $tpv->setFormHiddens([
                    'TransactionType' => '0',
                    'Amount' => '1,1',
                    'Order' => 'abcd1234',
                ]);

        return $tpv;
    }

    /**
     * @depends testInstance
     */
    public function testFormFields($tpv)
    {
        $tpv->setFormHiddens([
            'Amount' => '1,1',
            'Order' => '1234abcd',
            'MerchantURL' => 'http://example.com',
        ]);

        $values = $tpv->getFormValues();

        $this->assertTrue(count($values) === count(array_filter($values)));

        $fields = $tpv->getFormHiddens();

        $this->assertStringContainsString('<input', $fields);
        $this->assertStringContainsString('EP_SignatureVersion', $fields);
        $this->assertStringContainsString('EP_MerchantParameters', $fields);
        $this->assertStringContainsString('EP_Signature', $fields);

        return $tpv;
    }
   
    
}