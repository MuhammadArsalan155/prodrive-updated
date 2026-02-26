<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with(['student', 'course'])
            ->latest()
            ->paginate(10);
        return view('admin.certificates.index', compact('certificates'));
    }

    /**
     * Show form to create a new certificate
     */
    public function create(Request $request)
    {
        $students = Student::where('course_status', '2')->get();
        $courses = Course::where('is_active', true)->get();

        // If student_id is provided in query parameter, pre-select it
        $selectedStudentId = $request->get('student_id');
        $selectedStudent = null;

        if ($selectedStudentId) {
            $selectedStudent = Student::find($selectedStudentId);
        }

        return view('admin.certificates.create', compact('students', 'courses', 'selectedStudent'));
    }

    /**
     * Show eligible students for certificate generation
     */
    public function eligibleStudents()
    {
        // Get students who have completed their courses but don't have certificates yet
        $eligibleStudents = Student::where('course_status', '2')
            // ->whereDoesntHave('certificates')
            ->with('course')
            ->paginate(15);

        return view('admin.certificates.eligible', compact('eligibleStudents'));
    }

    /**
     * Store a newly created certificate
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if certificate already exists for this student and course
        $existingCertificate = Certificate::where('student_id', $request->student_id)->where('course_id', $request->course_id)->first();

        if ($existingCertificate) {
            return redirect()->back()->with('error', 'Certificate already exists for this student and course.');
        }

        // Create a unique certificate number
        $certificateNumber = 'CERT-' . Str::random(8) . '-' . time();

        // Generate verification URL - this will be used for QR code
        $verificationUrl = route('certificate.verify', ['certificate_number' => $certificateNumber]);

        // Store certificate in database
        $certificate = Certificate::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'certificate_number' => $certificateNumber,
            'issue_date' => Carbon::now(),
            'verification_url' => $verificationUrl,
        ]);

        // Generate and store the PDF certificate
        $this->generateCertificatePDF($certificate);

        return redirect()->route('admin.certificates.index')->with('success', 'Certificate generated successfully.');
    }

    /**
     * Generate certificate for a single student directly
     */
    // public function generateSingle($studentId)
    // {
    //     $student = Student::with('course')->find($studentId);

    //     if (!$student) {
    //         return redirect()->back()->with('error', 'Student not found.');
    //     }

    //     if ($student->course_status != '2') {
    //         return redirect()->back()->with('error', 'Student has not completed the course yet.');
    //     }

    //     if (!$student->course_id) {
    //         return redirect()->back()->with('error', 'Student does not have a course assigned.');
    //     }

    //     // Check if certificate already exists
    //     $existingCertificate = Certificate::where('student_id', $student->id)
    //         ->where('course_id', $student->course_id)
    //         ->first();

    //     if ($existingCertificate) {
    //         return redirect()->back()->with('error', 'Certificate already exists for this student.');
    //     }

    //     // Create a unique certificate number
    //     $certificateNumber = 'CERT-' . Str::random(8) . '-' . time();

    //     // Generate verification URL
    //     $verificationUrl = route('certificate.verify', ['certificate_number' => $certificateNumber]);

    //     // Store certificate in database
    //     $certificate = Certificate::create([
    //         'student_id' => $student->id,
    //         'course_id' => $student->course_id,
    //         'certificate_number' => $certificateNumber,
    //         'issue_date' => Carbon::now(),
    //         'verification_url' => $verificationUrl,
    //     ]);

    //     // Generate and store the PDF certificate
    //     $this->generateCertificatePDF($certificate);

    //     return redirect()->route('admin.certificates.index')->with('success', 'Certificate generated successfully for ' . $student->first_name . ' ' . $student->last_name . '.');
    // }

    public function generateSingle($studentId)
    {
        $student = Student::with('course')->find($studentId);

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        if ($student->course_status != '2') {
            return redirect()->back()->with('error', 'Student has not completed the course yet.');
        }

        if (!$student->course_id) {
            return redirect()->back()->with('error', 'Student does not have a course assigned.');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('student_id', $student->id)->where('course_id', $student->course_id)->first();

        if ($existingCertificate) {
            return redirect()->back()->with('error', 'Certificate already exists for this student.');
        }

        // Create a unique certificate number
        $certificateNumber = 'CERT-' . Str::random(8) . '-' . time();

        // Generate verification URL
        $verificationUrl = route('certificate.verify', ['certificate_number' => $certificateNumber]);

        // Store certificate in database
        $certificate = Certificate::create([
            'student_id' => $student->id,
            'course_id' => $student->course_id,
            'certificate_number' => $certificateNumber,
            'issue_date' => Carbon::now(),
            'verification_url' => $verificationUrl,
        ]);

        // Generate and store the PDF certificate
        $this->generateCertificatePDF($certificate);

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', 'Certificate generated successfully for ' . $student->first_name . ' ' . $student->last_name . '.');
    }

    /**
     * Generate certificates for multiple eligible students
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $count = 0;
        $errors = [];

        foreach ($request->student_ids as $studentId) {
            $student = Student::with('course')->find($studentId);

            // Debug: Log student details
            Log::info('Processing student ID: ' . $studentId, [
                'student_exists' => $student ? 'yes' : 'no',
                'course_id' => $student ? $student->course_id : 'N/A',
                'course_status' => $student ? $student->course_status : 'N/A',
            ]);

            if (!$student) {
                $errors[] = "Student with ID {$studentId} not found.";
                continue;
            }

            if ($student->course_status != '2') {
                $errors[] = "Student {$student->first_name} {$student->last_name} has not completed the course.";
                continue;
            }

            if (!$student->course_id) {
                $errors[] = "Student {$student->first_name} {$student->last_name} does not have a course assigned.";
                continue;
            }

            // Check if certificate already exists
            $existingCertificate = Certificate::where('student_id', $student->id)->where('course_id', $student->course_id)->first();

            if ($existingCertificate) {
                $errors[] = "Certificate already exists for {$student->first_name} {$student->last_name}.";
                continue;
            }

            try {
                // Create a unique certificate number
                $certificateNumber = 'CERT-' . Str::random(8) . '-' . time();

                // Generate verification URL
                $verificationUrl = route('certificate.verify', ['certificate_number' => $certificateNumber]);

                // Store certificate in database
                $certificate = Certificate::create([
                    'student_id' => $student->id,
                    'course_id' => $student->course_id,
                    'certificate_number' => $certificateNumber,
                    'issue_date' => Carbon::now(),
                    'verification_url' => $verificationUrl,
                ]);

                // Generate and store the PDF certificate
                $this->generateCertificatePDF($certificate);

                $count++;
            } catch (\Exception $e) {
                $errors[] = "Failed to generate certificate for {$student->first_name} {$student->last_name}: " . $e->getMessage();
                Log::error('Certificate generation failed', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $message = "{$count} certificates generated successfully.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(' ', $errors);
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with($count > 0 ? 'success' : 'error', $message);
    }

    /**
     * Display the specified certificate
     */
    public function show($id)
    {
        $certificate = Certificate::with(['student', 'course'])->findOrFail($id);
        return view('admin.certificates.show', compact('certificate'));
    }

    /**
     * Download the certificate
     */
    public function download($id)
    {
        $certificate = Certificate::findOrFail($id);

        if (!$certificate->certificate_path || !Storage::disk('public')->exists($certificate->certificate_path)) {
            $this->generateCertificatePDF($certificate);
            $certificate->refresh();
        }

        // Use response()->download with the full path to the file
        $path = storage_path('app/public/' . $certificate->certificate_path);

        // Make sure the file exists
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Certificate file could not be found.');
        }

        // Generate a nice filename for download
        $filename = 'Certificate_' . $certificate->student->first_name . '_' . $certificate->student->last_name . '.pdf';

        // Return the file as a download response with headers to force download
        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    /**
     * Regenerate the certificate PDF
     */
    public function regenerate($id)
    {
        $certificate = Certificate::findOrFail($id);
        $this->generateCertificatePDF($certificate, true);

        return redirect()->route('admin.certificates.show', $id)->with('success', 'Certificate regenerated successfully.');
    }

    /**
     * Delete the certificate
     */
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);

        // Delete the PDF file if it exists
        if ($certificate->certificate_path && Storage::disk('public')->exists($certificate->certificate_path)) {
            Storage::disk('public')->delete($certificate->certificate_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificates.index')->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Verify a certificate
     */
    public function verify(Request $request)
    {
        $certificateNumber = $request->certificate_number;
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->with(['student', 'course'])
            ->first();

        if (!$certificate) {
            return view('certificate.verify', ['valid' => false]);
        }

        return view('certificate.verify', [
            'valid' => true,
            'certificate' => $certificate,
        ]);
    }

    /**
     * Generate the certificate PDF
     */
    private function generateCertificatePDF($certificate, $force = false)
    {
        // Increase execution time for PDF generation
        set_time_limit(120);
        ini_set('max_execution_time', 120);

        // Skip generation if certificate already exists and not forced
        if (!$force && $certificate->certificate_path && Storage::disk('public')->exists($certificate->certificate_path)) {
            return;
        }

        // Get related data
        $student = Student::find($certificate->student_id);
        $course = Course::find($certificate->course_id);

        // Prepare data for PDF
        $data = [
            'certificate' => $certificate,
            'student' => $student,
            'course' => $course,
            'issue_date' => Carbon::parse($certificate->issue_date)->format('F d, Y'),
        ];

        // Generate PDF using DomPDF with settings for external QR API
        $html = View::make('admin.certificates.pdf', $data)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Enable external image loading
        $options->set('defaultFont', 'Arial');
        $options->set('isFontSubsettingEnabled', false);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();

        // Save PDF to storage
        $pdfPath = 'certificates/' . $certificate->certificate_number . '.pdf';
        Storage::disk('public')->put($pdfPath, $output);

        // Update certificate with file path
        $certificate->certificate_path = $pdfPath;
        $certificate->save();
    }
}
