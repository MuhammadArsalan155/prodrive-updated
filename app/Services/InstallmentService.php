<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Installment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallmentService
{
    /**
     * Create installment plan for a student's course enrollment
     * 
     * @param Course $course
     * @param Student $student
     * @return Invoice
     */
    public function createCourseInstallmentPlan(Course $course, Student $student)
    {
        return DB::transaction(function () use ($course, $student) {
            // Validate course has installment plan
            if (!$course->hasInstallmentPlan()) {
                throw new \InvalidArgumentException('Course does not support installment plans');
            }

            // Get installment plan
            $installmentPlan = $course->installmentPlan;

            // Calculate total course fee
            $totalCourseFee = $course->price;

            // Add setup fee if applicable
            if ($course->installment_setup_fee) {
                $totalCourseFee += $course->installment_setup_fee;
            }

            // Create invoice
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'invoice_number' => $this->generateUniqueInvoiceNumber(),
                'amount' => $totalCourseFee,
                'status' => 'pending',
                'is_installment_plan' => true,
                'total_installments' => $installmentPlan->number_of_installments,
                'installment_amount' => $totalCourseFee / $installmentPlan->number_of_installments
            ]);

            // Generate installment schedule
            $installmentSchedule = $installmentPlan->generateInstallmentSchedule($totalCourseFee);

            // Create installments
            foreach ($installmentSchedule as $index => $installmentData) {
                Installment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $installmentData['amount'],
                    'due_date' => $installmentData['due_date'],
                    'status' => 'pending',
                    'notes' => "Installment " . ($index + 1) . " of {$installmentPlan->number_of_installments}"
                ]);
            }

            // Create notification for installment plan
            $this->createInstallmentPlanNotification($student, $invoice);

            return $invoice;
        });
    }

    /**
     * Send installment reminders
     */
    public function sendInstallmentReminders()
    {
        // Upcoming installment reminders (7 days before due date)
        $upcomingInstallments = Installment::where('status', 'pending')
            ->whereDate('due_date', '=', Carbon::now()->addDays(7)->toDateString())
            ->get();

        foreach ($upcomingInstallments as $installment) {
            $this->sendInstallmentReminderNotification($installment);
        }

        // Overdue installment handling
        $overdueInstallments = Installment::where('status', 'pending')
            ->whereDate('due_date', '<', Carbon::now()->toDateString())
            ->get();

        foreach ($overdueInstallments as $installment) {
            $this->handleOverdueInstallment($installment);
        }
    }

    /**
     * Send installment reminder notification
     * 
     * @param Installment $installment
     */
    private function sendInstallmentReminderNotification(Installment $installment)
    {
        $student = $installment->invoice->student;

        Notification::create([
            'user_id' => $student->id,
            'type' => 'installment_reminder',
            'content' => sprintf(
                'Reminder: Your installment of $%.2f for %s is due on %s.',
                $installment->amount,
                $installment->invoice->course->name,
                $installment->due_date->format('F d, Y')
            )
        ]);

        // Optionally send email or SMS
        // $this->sendReminderEmail($student, $installment);
    }

    /**
     * Handle overdue installment
     * 
     * @param Installment $installment
     */
    private function handleOverdueInstallment(Installment $installment)
    {
        // Update installment status
        $installment->update(['status' => 'overdue']);

        $student = $installment->invoice->student;

        // Create overdue notification
        Notification::create([
            'user_id' => $student->id,
            'type' => 'installment_overdue',
            'content' => sprintf(
                'URGENT: Your installment of $%.2f for %s is overdue. Please make payment to avoid further action.',
                $installment->amount,
                $installment->invoice->course->name
            )
        ]);

        // Log overdue installment
        Log::warning('Overdue Installment', [
            'installment_id' => $installment->id,
            'student_id' => $student->id,
            'amount' => $installment->amount
        ]);
    }

    /**
     * Create initial installment plan notification
     * 
     * @param Student $student
     * @param Invoice $invoice
     */
    private function createInstallmentPlanNotification(Student $student, Invoice $invoice)
    {
        Notification::create([
            'user_id' => $student->id,
            'type' => 'installment_plan_created',
            'content' => sprintf(
                'Installment Plan Created: %s course, total $%.2f, %d installments',
                $invoice->course->name,
                $invoice->amount,
                $invoice->total_installments
            )
        ]);
    }

    /**
     * Generate unique invoice number
     * 
     * @return string
     */
    private function generateUniqueInvoiceNumber()
    {
        do {
            $invoiceNumber = 'INV-' . strtoupper(substr(uniqid(), -8));
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());

        return $invoiceNumber;
    }
}