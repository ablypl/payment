<?php

namespace Lari\Payments;


use Carbon\Carbon;

trait Payable
{
    /**
     * Payments
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function payments()
    {
        return $this->MorphMany(Payment::class, 'payable');
    }


    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function recordPayment($data)
    {
        return $this->payments()->create($data);
    }


    /**
     * @return mixed
     */
    public function getAmountPaidAttribute()
    {
        return $this->payments->sum('amount');
    }


    /**
     * @return string
     */
    public function getPaymentStatus()
    {
        $prefix = strtolower(class_basename($this));

        if ($this->isUnpaid()) {
            return $prefix . '.unpaid';
        }
        if ($this->isPaid()) {
            return $prefix . '.paid';
        }
        if ($this->isUnderpaid()) {
            return $prefix . '.underpaid';
        }
        if ($this->isOverpaid()) {
            return $prefix . '.overpaid';
        }
    }

    /**
     * @return mixed
     */
    public function getPaymentAttribute()
    {
        return $this->payments()->first();
    }

    public function getPaymentsOfType($type)
    {
        return $this->payments->filter(function($payment) use($type){
            return $payment->service == $type;
        });
    }



    /**
     * @return bool
     */
    public function isUnderpaid(): bool
    {
        return $this->payments && $this->amount_paid < $this->amount;
    }

    /**
     * @return bool
     */
    public function isUnpaid(): bool
    {
        return !$this->payments;
    }

    /**
     * @return bool
     */
    public function isOverpaid(): bool
    {
        return $this->payments && $this->amount_paid > $this->amount;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->amount == $this->amount_paid;
    }
    /**
     * @param $method
     * @param $parameters
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function __call($method, $parameters)
    {
        if (preg_match('/(?<=^|;)record([^;]+?)Payment(;|$)/', $method, $matches)) {
            $parameters = collect($parameters);

            return $this->recordPayment([
                'service'    => strtolower($matches[1]),
                'amount'     => $parameters->get(0),
                'service_id' => $parameters->get(1),
                'paid_at'    => $parameters->get(2) ? Carbon::parse($parameters->get(2)) : Carbon::now()
            ]);
        }

        return parent::__call($method, $parameters);
    }

}