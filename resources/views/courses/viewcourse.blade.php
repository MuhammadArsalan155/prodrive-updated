@extends('layouts.master')
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-2 text-gray-800">Courses Management</h1>
            <a href="{{ route('addcourse') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>Add New Course
            </a>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-check-circle mr-2"></i>Success!</strong> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle mr-2"></i>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3" style="background: #2a5c68;">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-white">Courses List</h6>
                    </div>
                    <div class="col text-right">
                        <a href="{{ route('addcourse') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add New Course
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Course Name</th>
                                <th>Type</th>
                                <th>Hours</th>
                                <th>Classes</th>
                                <th>Price</th>
                                <th>Installment Plan</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                                <tr>
                                    <td>{{ $course->id }}</td>
                                    <td>{{ $course->course_name }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $course->course_type == 'theory' ? 'info' : ($course->course_type == 'practical' ? 'warning' : 'primary') }}">
                                            {{ ucfirst($course->course_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($course->theory_hours > 0)
                                            <span class="badge badge-info">Theory: {{ $course->theory_hours }}h</span>
                                        @endif
                                        @if ($course->practical_hours > 0)
                                            <span class="badge badge-warning ml-1">Practical:
                                                {{ $course->practical_hours }}h</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($course->total_theory_classes) && $course->total_theory_classes > 0)
                                            <span class="badge badge-info">Theory: {{ $course->total_theory_classes }}</span>
                                        @endif
                                        @if (isset($course->total_practical_classes) && $course->total_practical_classes > 0)
                                            <span class="badge badge-warning ml-1">Practical:
                                                {{ $course->total_practical_classes }}</span>
                                        @endif
                                        @if ((!isset($course->total_theory_classes) || $course->total_theory_classes == 0) && 
                                             (!isset($course->total_practical_classes) || $course->total_practical_classes == 0))
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($course->course_price, 2) }}</td>
                                    <td>
                                        @if ($course->has_installment_plan && $course->installmentPlan)
                                            <span class="badge badge-success" data-toggle="tooltip" data-placement="top"
                                                data-original-title="
                                          <strong>Installment Plan:</strong> {{ $course->installmentPlan->Name }}<br>
                                          <strong>Number of Installments:</strong> {{ $course->installmentPlan->number_of_installments }}<br>
                                          <strong>First Installment Persentage:</strong> {{ $course->installmentPlan->first_installment_percentage }}%<br>
                                          <strong>Subsequent Installment Percentage:</strong> {{ $course->installmentPlan->subsequent_installment_percentage }}%
                                        ">
                                                Installment Available
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">No Installments</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $course->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $course->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($course->description)
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                data-target="#descriptionModal{{ $course->id }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>

                                            <!-- Description Modal -->
                                            <div class="modal fade" id="descriptionModal{{ $course->id }}" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ $course->course_name }} Description
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ $course->description }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No description</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('toggle_course_status', $course->id) }}" method="POST" class="mr-1">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm"
                                                    title="{{ $course->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-toggle-{{ $course->is_active ? 'on' : 'off' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('edit_course', $course) }}"
                                                class="btn btn-primary btn-sm mr-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('delete_course', $course->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "columnDefs": [{
                        "orderable": true,
                        "targets": [5, 6, 7]
                    }, // Price, Installment Plan and Status columns
                    {
                        "searchable": true,
                        "targets": [1, 2]
                    } // Course Name and Type columns
                ],
                "language": {
                    "emptyTable": "No courses available",
                    "zeroRecords": "No matching courses found"
                },
                "order": [
                    [0, "desc"]
                ] // Default sort by ID descending
            });
            // Auto close alerts
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);

            // Initialize tooltips
            function createTooltip(element) {
                // Remove any existing tooltips
                const existingTooltip = document.querySelector('.custom-tooltip');
                if (existingTooltip) {
                    existingTooltip.remove();
                }

                // Get tooltip content
                const title = element.getAttribute('data-original-title');
                if (!title) return;

                // Create tooltip element
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip';
                tooltip.innerHTML = title;

                // Position the tooltip
                const rect = element.getBoundingClientRect();
                tooltip.style.position = 'fixed';
                tooltip.style.left = `${rect.left}px`;
                tooltip.style.top = `${rect.bottom + 5}px`;
                tooltip.style.zIndex = '1000';
                tooltip.style.backgroundColor = 'black';
                tooltip.style.color = 'white';
                tooltip.style.padding = '5px 10px';
                tooltip.style.borderRadius = '4px';
                tooltip.style.maxWidth = '300px';
                tooltip.style.wordWrap = 'break-word';

                // Add to body
                document.body.appendChild(tooltip);

                // Remove tooltip when mouse leaves
                function removeTooltip() {
                    tooltip.remove();
                    element.removeEventListener('mouseleave', removeTooltip);
                }

                element.addEventListener('mouseleave', removeTooltip);
            }

            $('[data-toggle="tooltip"]').each(function() {
                $(this).on('mouseenter', function() {
                    createTooltip(this);
                });
            });
        });
    </script>
@endsection