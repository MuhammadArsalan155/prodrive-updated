<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::latest()->get();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_price' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'additional_price' => $request->additional_price ?? 0,
            'is_active' => $request->has('is_active')
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('payment-methods', 'public');
            $data['logo'] = $logoPath;
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method added successfully');
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_price' => 'nullable|numeric|min:0|max:999999.99',
            'is_active' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'additional_price' => $request->additional_price ?? 0,
            'is_active' => $request->has('is_active')
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }

            $logo = $request->file('logo');
            $logoPath = $logo->store('payment-methods', 'public');
            $data['logo'] = $logoPath;
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Payment method updated successfully');
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        // Check if payment method is in use
        if ($paymentMethod->payments()->exists()) {
            return back()->with('error', 'Cannot delete payment method as it is being used in transactions');
        }

        // Delete logo if exists
        if ($paymentMethod->logo) {
            Storage::disk('public')->delete($paymentMethod->logo);
        }

        $paymentMethod->delete();
        return back()->with('success', 'Payment method deleted successfully');
    }

    public function toggleStatus($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->update([
            'is_active' => !$paymentMethod->is_active
        ]);

        $status = $paymentMethod->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Payment method {$status} successfully");
    }
}
