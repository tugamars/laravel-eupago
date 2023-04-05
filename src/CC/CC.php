<?php

namespace CodeTech\EuPago\CC;

use Carbon\Carbon;
use CodeTech\EuPago\EuPago;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;

class CC extends EuPago
{
    /**
     * The unique resource identifier.
     */
    const URI = '/api/v1.02/creditcard/create';

    /**
     * The payment value.
     *
     * @var float
     */
    protected $value;

    /**
     * External identifier. Ex: the order id.
     *
     * @var int
     */
    protected $id;

    /**
     * Wheter or not to notify the client via email
     *
     * @var boolean
     */
    protected $notify;

    /**
     * Client's email
     *
     * @var string
     */
    protected $email;

    /**
     * Payment's back url
     *
     * @var string
     */
    protected $backUrl;

    protected $errors = [];

    /**
     * MB constructor.
     *
     * @param float $value
     * @param string $id
     * @param string $backUrl
     * @param bool $notify
     * @param string $email
     */
    public function __construct(float $value, string $id, string $email, string $backUrl, bool $notify=false)
    {
        $this->value            = $value;
        $this->id               = $id;
        $this->notify               = $notify;
        $this->email               = $email;
        $this->backUrl               = $backUrl;
    }

    /**
     * Returns the errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Adds an error to the bag.
     *
     * @param $code
     * @param $message
     */
    protected function addError($code, $message)
    {
        $this->errors[$code] = html_entity_decode($message);
    }

    /**
     * Determines whether errors are logged.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Generates a new MBWay reference.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(): array
    {
        $client = new Client(['base_uri' => $this->getBaseUri(), 'headers'=>[
            'Authorization'=>config('eupago.api_key')
        ]]);



        try {
            $response = $client->post(self::URI, $this->getParams());
        } catch (\Exception $e) {
            throw $e;
        }

        $referenceData = json_decode($response->getBody()->getContents(), true);

        if ($referenceData['transactionStatus'] !== "Success") {
            $this->addError($referenceData['code'], $referenceData['text']);
        }

        return $this->mappedReferenceKeys($referenceData);
    }

    /**
     * Maps the reference data keys.
     *
     * @param array $referenceData
     * @return array
     */
    protected function mappedReferenceKeys(array $referenceData): array
    {
        return [
            'transactionStatus' => $referenceData['transactionStatus'] ?? null,
            'transactionID' => $referenceData['transactionID'] ?? null,
            'reference' => $referenceData['reference'] ?? null,
            'redirectUrl' => $referenceData['redirectUrl'] ?? null,
        ];
    }

    /**
     * Returns the required params for making a request.
     *
     * @return array
     */
    protected function getParams(): array
    {
        return [
            'payment'=>[
                'identifier'=>$this->id,
                'amount'=>[
                    'value'=>$this->value,
                    'currency'=>config('eupago.currency'),
                ],
                'successUrl'=>config('eupago.cc_success_url')."?order=".$this->id,
                'failUrl'=>config('eupago.cc_fail_url')."?order=".$this->id,
                'backUrl'=>$this->backUrl,
                'lang'=>strtoupper(App::getLocale())
            ],
            'customer'=>[
                'notify'=>$this->notify,
                'email'=>$this->email
            ]
        ];
    }
}
