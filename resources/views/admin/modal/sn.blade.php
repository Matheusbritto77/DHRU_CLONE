<!-- Modal Background and Content -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="formModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-gray-800 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal Content -->
        <div class="inline-block align-bottom bg-black rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('edit_service') }}" method="POST" class="w-full" id="serviceForm">
                @csrf
                <!-- Modal Header -->
                <div class="bg-black px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-white" id="modalTitle">Service Details</h1>
                        <button type="button" class="text-white focus:outline-none" onclick="closeModal()">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Service Name -->
                    <div class="mb-4">
                        <label for="serviceName" class="block text-sm font-medium text-white">Service Name</label>
                        <input type="text" id="serviceName" name="serviceName" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" placeholder="Enter service name...">
                    </div>

                    <!-- Time -->
                    <div class="mb-4">
                        <label for="time" class="block text-sm font-medium text-white">Time</label>
                        <input type="text" id="time" name="time" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" placeholder="Enter time...">
                    </div>

                    <!-- Cost -->
                    <div class="mb-4">
                        <label for="cost" class="block text-sm font-medium text-white">Cost</label>
                        <input type="text" id="cost" name="cost" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" placeholder="Enter cost...">
                    </div>

                    <!-- Custom Fields -->
                    <div class="mb-4">
                        <label for="customFields" class="block text-sm font-medium text-white" id="customFieldsLabel">Custom Fields</label>
                        <input type="text" id="customFields" name="customFields" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" placeholder="Enter custom fields...">
                    </div>

                    <!-- Service ID -->
                    <div class="mb-4">
                        <label for="serviceID" class="block text-sm font-medium text-white">Service ID</label>
                        <input type="text" id="serviceID" name="serviceID" class="form-input w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" readonly>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end">
                        <button type="button" class="inline-block bg-gray-700 text-gray-300 py-2 px-4 ml-2 rounded-md hover:bg-gray-600 focus:outline-none focus:bg-gray-600" onclick="closeModal()">Close</button>
                        <button type="submit" class="inline-block bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Function to open modal with service data
    function openModal(serviceData) {
        // Populate modal fields with service data
        $('#serviceName').val(serviceData.SERVICENAME);
        $('#time').val(serviceData.TIME);
        $('#cost').val(serviceData.CREDIT);
        $('#customFields').val(serviceData.customname || serviceData.fieldname || ''); // Populate custom fields

        $('#serviceID').val(serviceData.SERVICEID);

        // Update Modal Title
        $('#modalTitle').text(serviceData.customname || 'Service Details');

        // Update Custom Fields Label
        let customFieldsLabel = serviceData.customname || 'Custom Fields';
        $('#customFieldsLabel').text(customFieldsLabel);

        // Show the modal
        $('#formModal').removeClass('hidden');
        $('body').addClass('overflow-hidden'); // Prevent scrolling of background content
    }

    // Function to close modal
    function closeModal() {
        $('#formModal').addClass('hidden');
        $('body').removeClass('overflow-hidden'); // Enable scrolling of background content
    }

    // Handle form submission via AJAX
    $('#serviceForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        submitForm(); // Call your custom submitForm function
    });

    // Custom function to handle form submission via AJAX
    function submitForm() {
        // Serialize form data manually
        var formData = $('#serviceForm').serialize();

        // Log the form data to console
        console.log('Form Data:', formData);

        // Submit the form using AJAX
        $.ajax({
            url: $('#serviceForm').attr('action'), // Ensure it uses the form action attribute
            type: 'POST',
            data: formData,
            success: function(response) {
                // Check if response indicates success
                if (response.success) {
                    // Display success alert using SweetAlert2
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeModal(); // Close modal after successful submission
                } else {
                    // Display error alert using SweetAlert2 for specific error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr, status, error) {
                // Display generic error alert using SweetAlert2 for AJAX error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while submitting the form.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    }
</script>
