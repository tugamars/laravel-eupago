<?php

namespace CodeTech\EuPago\Traits;

use CodeTech\EuPago\CC\CC;
use CodeTech\EuPago\Models\CCTransaction;
use CodeTech\EuPago\Models\MbReference;

trait Ccable
{
    /**
     * Get all of the model's CC references.
     */
    public function ccReferences()
    {
        return $this->morphMany(CCTransaction::class, 'ccable');
    }

    /**
     * Creates a CC reference.
     *
     * @param float $value
     * @param string $id
     * @param string $backUrl
     * @param bool $notify
     * @param string $email
     * @return array
     * @throws \Exception
     */
    public function createCcReference(float $value, string $id, string $email, string $backUrl, bool $notify=false)
    {
        $cc = new CC(
            $value,
            $id,
            $email,
            $backUrl,
            $notify
        );

        try {
            $ccReferenceData = $cc->create();
        } catch (\Exception $e) {
            throw $e;
        }

        if ($cc->hasErrors()) {
            return $cc->getErrors();
        }

        $this->ccReferences()->create($ccReferenceData);
    }
}
