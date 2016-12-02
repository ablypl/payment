<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Lari\Payments\Events\NewPaymentRecorded;

class Payment extends Model
{
    protected $guarded = [];

    protected $dates = ['paid_at'];


    public static function record(array $data): Payment
    {
        $payment = static::create($data);

        event(new NewPaymentRecorded($payment));

        return $payment;
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($payment) {
            $payment->paid_at = $payment->paid_at ?? \Carbon\Carbon::now();
        });
    }

    /**
     * Payable
     * Define a relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function payable()
    {
        return $this->morphTo();
    }

}
