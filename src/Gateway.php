<?php

namespace Omnipay\Sermepa;

use Symfony\Component\HttpFoundation\Request;
use Omnipay\Common\AbstractGateway;
use Omnipay\Sermepa\Message\CallbackResponse;

/**
 * Sermepa (Redsys) Gateway
 *
 * @author Javier Sampedro <jsampedro77@gmail.com>
 * @author NitsNets Studio <github@nitsnets.com>
 */
class Gateway extends AbstractGateway
{

    public function getDefaultParameters()
    {
        return array(
            'currency' => '978',
            'terminal' => '001',
            'consumerLanguage' => '001',
            'transactionType' => '0',
            'payMethods' => 'C',
            'titular' => '',
            'merchantName' => '',
            'merchantKey' => '',
            'merchantCode' => '',
            'merchantURL' => '',
            'returnUrl' => '',
            'cancelUrl' => '',
            'testMode' => false
        );
    }

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

    public function setPayMethods($payMethods)
    {
        $this->setParameter('payMethods', $payMethods);
    }

    public function setReturnUrl($returnUrl)
    {
        $this->setParameter('returnUrl', $returnUrl);
    }

    public function setCancelUrl($cancelUrl)
    {
        $this->setParameter('cancelUrl', $cancelUrl);
    }

    public function setTransactionType($transactionType)
    {
        $this->setParameter('transactionType', $transactionType);
    }

    /**
     * Sets the identifier parameter. This parameter is used to flag in our request that we want a token back or to
     * send our token.
     *
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->setParameter('identifier', $identifier);
    }

    public function getName()
    {
        return 'Sermepa';
    }

    public function purchase(array $parameters = array())
    {
        if (isset($parameters['recurrent']) && $parameters['recurrent']) {
            return $this->createRequest('\Omnipay\Sermepa\Message\RecurrentPurchaseRequest', $parameters);
        } else {
            return $this->createRequest('\Omnipay\Sermepa\Message\PurchaseRequest', $parameters);
        }
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function checkCallbackResponse(Request $request)
    {
        $response = new CallbackResponse($request, $this->getParameter('merchantKey'));

        return $response->isSuccessful();
    }

    public function decodeCallbackResponse(Request $request)
    {
        return json_decode(base64_decode(strtr($request->get('Ds_MerchantParameters'), '-_', '+/')), true);
    }
}
