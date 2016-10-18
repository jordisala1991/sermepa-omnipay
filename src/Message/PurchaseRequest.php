<?php

namespace Omnipay\Sermepa\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Sermepa\Encryptor\Encryptor;

/**
 * Sermepa (Redsys) Purchase Request
 *
 * @author Javier Sampedro <jsampedro77@gmail.com>
 * @author NitsNets Studio <github@nitsnets.com>
 */
class PurchaseRequest extends AbstractRequest
{

    protected $liveEndpoint = 'https://sis.redsys.es';
    protected $testEndpoint = 'https://sis-t.redsys.es:25443';

    public function setMerchantName($merchantName)
    {
        $this->setParameter('merchantName', $merchantName);
    }

    public function setTitular($titular)
    {
        return $this->setParameter('titular', $titular);
    }

    public function setMerchantKey($merchantKey)
    {
        $this->setParameter('merchantKey', $merchantKey);
    }

    public function setMerchantCode($merchantCode)
    {
        $this->setParameter('merchantCode', $merchantCode);
    }

    public function setMerchantURL($merchantURL)
    {
        $this->setParameter('merchantURL', $merchantURL);
    }

    public function setTerminal($terminal)
    {
        $this->setParameter('terminal', $terminal);
    }

    public function setConsumerLanguage($consumerLanguage)
    {
        $this->setParameter('consumerLanguage', $consumerLanguage);
    }

    public function setPayMethod($payMethod)
    {
        $this->setParameter('payMethod', $payMethod);
    }

    public function setTransactionType($transactionType)
    {
        $this->setParameter('transactionType', $transactionType);
    }

    public function getData()
    {
        $data = array();

        $data['Ds_Merchant_Amount'] = (float)$this->getAmount();
        $data['Ds_Merchant_Currency'] = $this->getCurrency();
        $data['Ds_Merchant_Order'] = $this->getToken();
        $data['Ds_Merchant_ProductDescription'] = $this->getDescription();

        $data['Ds_Merchant_Titular'] = $this->getParameter('titular');
        $data['Ds_Merchant_ConsumerLanguage'] = $this->getParameter('consumerLanguage');
        $data['Ds_Merchant_MerchantCode'] = $this->getParameter('merchantCode');
        $data['Ds_Merchant_MerchantName'] = $this->getParameter('merchantName');
        $data['Ds_Merchant_MerchantURL'] = $this->getParameter('merchantURL');
        $data['Ds_Merchant_Terminal'] = $this->getParameter('terminal');
        $data['Ds_Merchant_TransactionType'] = $this->getParamter('transactionType');
        $data['Ds_Merchant_PayMethod'] = $this->getParameter('payMethod');

        $data['Ds_Merchant_UrlOK'] = $this->getReturnUrl();
        $data['Ds_Merchant_UrlKO'] = $this->getCancelUrl();

        if (!empty($this->getParameter('identifier'))) {
            $data['Ds_Merchant_Identifier'] = $this->getParameter('identifier');
        }

        $merchantParameters = base64_encode(json_encode($data));

        return array(
            'Ds_MerchantParameters' => $merchantParameters,
            'Ds_Signature' => $this->generateSignature($merchantParameters),
            'Ds_SignatureVersion' => 'HMAC_SHA256_V1'
        );

    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    public function getEndpoint()
    {
        return $this->getEndpointBase().'/sis/realizarPago';
    }

    protected function getEndpointBase()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function generateSignature($merchantParameters)
    {
        $key = base64_decode($this->getParameter('merchantKey'));
        $key = Encryptor::encrypt_3DES($this->getToken(), $key);
        $res = hash_hmac('sha256', $merchantParameters, $key, true);

        return base64_encode($res);
    }
}
