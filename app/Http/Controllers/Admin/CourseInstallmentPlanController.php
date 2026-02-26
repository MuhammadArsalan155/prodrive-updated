<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseInstallmentPlan;
use Illuminate\Http\Request;

class CourseInstallmentPlanController extends Controller
{
    public function index()
    {
        $courseInstallmentPlans = CourseInstallmentPlan::where('is_active', true)
            ->latest()
            ->paginate(10);

        return view('admin.course-installment-plans.index', compact('courseInstallmentPlans'));
    }

    public function create()
    {
        return view('admin.course-installment-plans.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Name' => 'required|string|max:255|unique:course_installment_plans,Name',
            'number_of_installments' => 'required|integer|min:1|max:12',
            'first_installment_percentage' => 'required|numeric|min:0|max:100',
            'subsequent_installment_percentage' => 'required|numeric|min:0|max:100',
            'days_between_installments' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'course_duration_months' => 'required|integer|min:1|max:36',
        ]);

        // Ensure total percentages equal 100%
        if (($validatedData['first_installment_percentage'] + $validatedData['subsequent_installment_percentage']) !== 100) {
            return back()->withInput()->with('error', 'Total installment percentages must sum up to 100%');
        }

        CourseInstallmentPlan::create($validatedData);

        return redirect()->route('admin.course-installment-plans.index')
            ->with('success', 'Installment Plan created successfully.');
    }

    public function show(CourseInstallmentPlan $courseInstallmentPlan)
    {
        return view('admin.course-installment-plans.show', compact('courseInstallmentPlan'));
    }

    public function edit(CourseInstallmentPlan $courseInstallmentPlan)
    {
        return view('admin.course-installment-plans.edit', compact('courseInstallmentPlan'));
    }

    public function update(Request $request, CourseInstallmentPlan $courseInstallmentPlan)
    {
        $validatedData = $request->validate([
            'Name' => 'required|string|max:255|unique:course_installment_plans,Name,' . $courseInstallmentPlan->id,
            'number_of_installments' => 'required|integer|min:1|max:12',
            'first_installment_percentage' => 'required|numeric|min:0|max:100',
            'subsequent_installment_percentage' => 'required|numeric|min:0|max:100',
            'days_between_installments' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'course_duration_months' => 'required|integer|min:1|max:36',
        ]);

        // Ensure total percentages equal 100%
        if (($validatedData['first_installment_percentage'] + $validatedData['subsequent_installment_percentage']) !== 100) {
            return back()->withInput()->with('error', 'Total installment percentages must sum up to 100%');
        }

        $courseInstallmentPlan->update($validatedData);

        return redirect()->route('admin.course-installment-plans.index')
            ->with('success', 'Installment Plan updated successfully.');
    }

    public function destroy(CourseInstallmentPlan $courseInstallmentPlan)
    {
        $courseInstallmentPlan->delete();

        return redirect()->route('admin.course-installment-plans.index')
            ->with('success', 'Installment Plan deleted successfully.');
    }

    public function createDefaultPlans()
    {
        CourseInstallmentPlan::createDefaultPlans();

        return redirect()->route('admin.course-installment-plans.index')
            ->with('success', 'Default plans created successfully.');
    }
}