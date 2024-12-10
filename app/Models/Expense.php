<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'expense_type', 'payment_category_id', 'description', 'date', 'payment_type'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paymentCategory() {
        return $this->belongsTo(PaymentCategory::class);
    }

}
