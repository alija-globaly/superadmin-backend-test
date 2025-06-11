<?php

namespace Agentcis\Tenant\DTOs;

class InvitationDTO
{
    private $email;
    private $isPaid;
    private $plan;
    private $currency;
    private $billingCycle;
    private $referrer;

    public function __construct($email, $isPaid, $plan = "starter", $currency = "AUD", $billingCycle = "monthly", $referrer = null)
    {
        $this->email = $email;
        $this->isPaid = $isPaid;
        $this->plan = $plan;
        $this->currency = $currency;
        $this->billingCycle = $billingCycle;
        $this->referrer = $referrer;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getIsPaid()
    {
        return $this->isPaid;
    }

    public function getPlan()
    {
        return $this->plan;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getBillingCycle()
    {
        return $this->billingCycle;
    }

    public function getReferrer()
    {
        return $this->referrer;
    }

    public function toArray()
    {
        return [
            'email' => $this->email,
            'is_paid' => $this->isPaid,
            'plan' => $this->plan,
            'currency' => $this->currency,
            'billing_cycle' => $this->billingCycle,
            'referrer' => $this->referrer,
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
