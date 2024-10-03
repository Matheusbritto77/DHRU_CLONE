<!-- Modal Background and Content -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="formModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal Content -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('submit_serial_number') }}" method="POST" class="w-full" id="serialForm">
                @csrf
                <!-- Modal Header -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-white" id="customName">Service Details</h1>
                        <button type="button" class="text-white focus:outline-none" onclick="closeModal()">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Service Name -->
                    <div class="mb-4">
                        <label for="serviceName" class="block text-sm font-medium text-white">Service Name</label>
                        <p class="text-lg font-semibold text-white" id="serviceName"></p>
                    </div>

                    <!-- Hidden Input for Service Name -->
                    <input type="hidden" id="servicename" name="servicename">

                    <!-- Time -->
                    <div class="mb-4">
                        <label for="time" class="block text-sm font-medium text-white">Time</label>
                        <p class="text-lg font-semibold text-white" id="time"></p>
                    </div>

                    <!-- Cost -->
                    <div class="mb-4">
                        <label for="COST" class="block text-sm font-medium text-white">Cost</label>
                        <p class="text-lg font-semibold text-white" id="costDisplay"></p>
                        <input type="hidden" id="COST" name="COST"> <!-- Campo hidden para enviar COST -->
                    </div>

                    <!-- Serial Input -->
                    <div class="mb-4">
                        <label for="SERIAL_NUMBER" class="block text-sm font-medium text-white" id="serialLabel">Serial</label>
                        <input type="text" id="SERIAL_NUMBER" name="SERIAL_NUMBER" class="form-input w-full py-2 px-3 border border-white-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" placeholder="Enter ...">
                    </div>

                    <!-- Hidden Input for SERVICEID -->
                    <input type="hidden" id="SERVICEID" name="SERVICEID">

                    <!-- Display SERVICEID -->
                    <div class="mb-4">
                        <label for="ID" class="block text-sm font-medium text-white">Service ID</label>
                        <p class="text-lg font-semibold text-white" id="ID"></p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end">
                        <button type="button" class="inline-block bg-gray-300 text-gray-700 py-2 px-4 ml-2 rounded-md hover:bg-gray-400 focus:outline-none focus:bg-gray-400" onclick="closeModal()">Close</button>
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
        $('#serviceName').text(serviceData.SERVICENAME);
        $('#servicename').val(serviceData.SERVICENAME); // Set servicename in hidden input
        $('#time').text(serviceData.TIME);
        $('#costDisplay').text('$' + serviceData.CREDIT); // Display COST
        $('#COST').val(serviceData.CREDIT); // Set COST in hidden input
        $('#SERVICEID').val(serviceData.SERVICEID); // Set SERVICEID in hidden input
        $('#ID').text(serviceData.SERVICEID); // Display SERVICEID in paragraph

        // Update Serial Label based on customname or fieldname
        let customname = serviceData.customname || "Informacao Necessária";
        let fieldname = serviceData.fieldname || "Campo Necessário";

        $('#serialLabel').text(customname);

        // Set the hidden input for fieldname
        $('#serialFieldname').val(fieldname);

        // Clear previous dynamic fields, if any
        $('#dynamicFieldsSection').empty();

        // Create dynamic fields based on fieldname values
        if (serviceData.fieldname) {
            var fieldnames = serviceData.fieldname.split(',');

            if (fieldnames.length > 0) {
                var maxFields = Math.min(fieldnames.length, 7); // Limit to a maximum of 7 fields

                for (var i = 0; i < maxFields; i++) {
                    var currentFieldname = fieldnames[i].trim() || "Campo Necessário"; // Use default name if empty
                    var inputField = "<div class='mb-4'>" +
                                        "<label for='" + currentFieldname.toLowerCase().replace(/\s+/g, '_') + "' class='block text-sm font-medium text-white-700'>" + currentFieldname + "</label>" +
                                        "<input type='text' id='" + currentFieldname.toLowerCase().replace(/\s+/g, '_') + "' name='" + currentFieldname.toLowerCase().replace(/\s+/g, '_') + "' class='form-input w-full py-2 px-3 border border-white-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm' placeholder='Enter ...'> " +
                                    '</div>';
                    $('#dynamicFieldsSection').append(inputField);
                }
            }
        }

        // Show or hide quantity field based on MINQNT
        if (serviceData.MINQNT > 0) {
            $('#QntField').removeClass('hidden');
            $('#minQnt').text(serviceData.MINQNT); // Display minimum
            $('#maxQnt').text(serviceData.MAXQNT); // Display maximum
        } else {
            $('#QntField').addClass('hidden');
        }

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
    $('#serialForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        submitForm(); // Call your custom submitForm function
    });

    // Custom function to handle form submission via AJAX
    function submitForm() {
        // Serialize form data manually
        var formData = $('#serialForm').serialize();

        // Log the form data to console
        console.log('Form Data:', formData);

        // Submit the form using AJAX
        $.ajax({
            url: $('#serialForm').attr('action'), // Ensure it uses the form action attribute
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

<!-- Your HTML form structure -->
<form id="serialForm" action="/your-action-url" method="POST">
    <!-- Other form fields -->
    <input type="hidden" id="serialFieldname" name="fieldname" value="">
</form>
