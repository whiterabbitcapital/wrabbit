<?php

namespace App\Rules;

use App\Models\Transaction;
use Illuminate\Contracts\Validation\Rule;

class TnxRef implements Rule
{
    /**
     * Transaction object
     *
     * @var  \App\Models\Transaction
     */
    private $transaction;

    /**
     * Represents if the found transaction belongs to the same authenticated user
     *
     * @var  bool
     */
    private $sameTnxUser = false;

    /**
     * Create a new rule instance.
     *
     * @param  string
     * @return  void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $reference = trim_input($value);
        $transactions = Transaction::method($this->transaction->tnx_method)
                                ->where('reference', $reference)
                                ->get();

        if (filled($transactions)) {
            if ($transactions->where('user_id', $this->transaction->user_id)->count()) {
                $this->sameTnxUser = true;
            }

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->sameTnxUser == true) {
            return __(':Attribute already used in past transaction.');
        }

        return __('The :attribute is invalid or in used.');
    }
}
