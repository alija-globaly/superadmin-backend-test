<?php

namespace App\Agentcis\Core\Subscription\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use JsonSerializable;

class SubscriptionDetail implements Arrayable, JsonSerializable
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $status;
    /**
     * @var string
     */
    private $currencyCode;
    /**
     * @var string
     */
    private $startedAt;
    /**
     * @var string
     */
    private $trialEnd;
    /**
     * @var string
     */
    private $nextBillingAt;
    /**
     * @var string
     */
    private $billingPeriodUnit;
    /**
     * @var string
     */
    private $paymentSourceId;
    /**
     * @var array
     */
    private $addOns;
    /**
     * @var float
     */
    private $nextBillingAmount;
    /**
     * @var string
     */
    private $cancelledAt;

    public function __construct(
        string $id,
        string $status,
        string $currencyCode,
        string $startedAt,
        string $nextBillingAt = null,
        string $cancelledAt,
        string $billingPeriodUnit,
        array $addOns,
        float $nextBillingAmount,
        string $trialEnd = null,
        string $paymentSourceId = null
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->currencyCode = $currencyCode;
        $this->startedAt = $startedAt;
        $this->nextBillingAt = $nextBillingAt;
        $this->billingPeriodUnit = $billingPeriodUnit;
        $this->addOns = $addOns;
        $this->nextBillingAmount = $nextBillingAmount;
        $this->trialEnd = $trialEnd;
        $this->paymentSourceId = $paymentSourceId;
        $this->cancelledAt = $cancelledAt;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return string
     */
    public function getStartedAt(): string
    {
        return $this->startedAt;
    }

    /**
     * @return string
     */
    public function getTrialEnd(): ?string
    {
        return $this->trialEnd;
    }

    /**
     * @return string
     */
    public function getPaymentSourceId(): ?string
    {
        return $this->paymentSourceId;
    }

    /**
     * @return string
     */
    public function getNextBillingAt()
    {
        if(is_null($this->nextBillingAt)) return null;
        return Carbon::parse($this->nextBillingAt)->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function getBillingPeriodUnit(): string
    {
        return $this->billingPeriodUnit;
    }

    /**
     * @return array
     */
    public function getAddOns(): array
    {
        return $this->addOns;
    }

    /**
     * @return float
     */
    public function getNextBillingAmount(): float
    {
        return $this->nextBillingAmount;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'status' => $this->getStatus(),
            'currency_cde' => $this->currencyCode,
            'started_at' => $this->startedAt,
            'trial_ends_at' => $this->trialEnd,
            'cancelled_at' => $this->cancelledAt,
            'next_billing_at' => $this->getNextBillingAt(),
            'next_billing_amount' => $this->nextBillingAmount,
            'billing_period_unit' => $this->billingPeriodUnit,
            'add_ons' => $this->addOns,
            'payment_source_id' => $this->paymentSourceId
        ];
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return (int)$this->status;
    }

    /**
     * @return string
     */
    public function getCancelledAt(): string
    {
        return $this->cancelledAt;
    }

}
