<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInstallmentPlan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'Name',
        'number_of_installments',
        'first_installment_percentage',
        'subsequent_installment_percentage',
        'days_between_installments',
        'is_active',
        'course_duration_months'
    ];

    // Relationship with Course (One-to-Many)
    public function courses()
    {
        return $this->hasMany(Course::class, 'course_installment_plan_id');
    }

    // Generate installment schedule
    public function generateInstallmentSchedule(
        $totalCourseFee, 
        Carbon $firstInstallmentDate = null
    ) {
        if (!$firstInstallmentDate) {
            $firstInstallmentDate = Carbon::now();
        }

        $installments = [];
        $remainingAmount = $totalCourseFee;

        // First installment calculation
        $firstInstallmentAmount = round(
            $totalCourseFee * ($this->first_installment_percentage / 100), 
            2
        );
        $installments[] = [
            'amount' => $firstInstallmentAmount,
            'due_date' => $firstInstallmentDate,
        ];

        $remainingAmount -= $firstInstallmentAmount;

        // Subsequent installments
        $subsequentInstallmentAmount = round(
            $remainingAmount / ($this->number_of_installments - 1), 
            2
        );

        $subsequentInstallmentDate = $firstInstallmentDate->copy();

        for ($i = 1; $i < $this->number_of_installments; $i++) {
            // Move to next installment date
            $subsequentInstallmentDate->addDays($this->days_between_installments);

            // Adjust last installment to account for any rounding differences
            $installmentAmount = ($i == $this->number_of_installments - 1) 
                ? $remainingAmount 
                : $subsequentInstallmentAmount;

            $installments[] = [
                'amount' => $installmentAmount,
                'due_date' => $subsequentInstallmentDate,
            ];

            $remainingAmount -= $installmentAmount;
        }

        return $installments;
    }

    // Static method to create default installment plans
    public static function createDefaultPlans()
    {
        $defaultPlans = [
            [
                'Name' => '2-Month Installment Plan',
                'number_of_installments' => 2,
                'first_installment_percentage' => 50,
                'subsequent_installment_percentage' => 50,
                'days_between_installments' => 90,
                'course_duration_months' => 6,
                'is_active' => true
            ],
            [
                'Name' => '3-Month Installment Plan',
                'number_of_installments' => 3,
                'first_installment_percentage' => 40,
                'subsequent_installment_percentage' => 30,
                'days_between_installments' => 60,
                'course_duration_months' => 6,
                'is_active' => true
            ]
        ];

        foreach ($defaultPlans as $planData) {
            self::create($planData);
        }
    }
}