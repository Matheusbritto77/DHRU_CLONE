<!-- Modal Background and Content -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="formModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-gray-200 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal Content -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form action="{{ route('submit_server') }}" method="POST" class="w-full" id="serialForm">
                @csrf
                <!-- Modal Header -->
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold text-white" id="customName">Service Details</h1>
                        <button type="button" class="text-white hover:text-white-600 focus:outline-none" onclick="closeModal()">
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

                    <!-- Quantidade Field (Hidden) -->
                    <div id="QntField" class="mb-4 hidden">
                        <label for="quantidade" class="block text-sm font-medium text-white">Quantidade</label>
                        <p class="text-sm text-white mb-2">Mínimo: <span id="minQnt"></span>, Máximo: <span id="maxQnt"></span></p>
                        <input type="number" id="Qnt" name="Qnt" class="form-input w-full py-2 px-3 border border-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-black" placeholder="Enter ..." min="1">
                    </div>
                    
                    <!-- Dynamic Fields Section -->
                    <div class="text-lg font-semibold text-white" id="dynamicFieldsSection">
                        <!-- Campos dinâmicos serão inseridos aqui -->
                    </div>

                    <!-- Display SERVICEID -->
                    <div class="mb-4">
                        <label for="ID" class="block text-sm font-medium text-white">Service ID</label>
                        <p class="text-lg font-semibold text-white" id="ID"></p>
                    </div>

                    <!-- Hidden Input for SERVICEID -->
                    <input type="hidden" id="SERVICEID" name="SERVICEID">

                    <!-- Modal Footer -->
                    <div class="flex justify-end">
                        <button type="button" class="inline-block bg-white text-white-700 py-2 px-4 ml-2 rounded-md hover:bg-white-400 focus:outline-none focus:bg-white-400" onclick="closeModal()">Close</button>
                        <button type="button" class="inline-block bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600" onclick="submitForm()">Submit</button>
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
        $('#costDisplay').text('$' + serviceData.CREDIT); // Display initial COST
        $('#COST').val(serviceData.CREDIT); // Set initial COST in hidden input
        $('#SERVICEID').val(serviceData.SERVICEID); // Set SERVICEID in hidden input
        $('#ID').text(serviceData.SERVICEID); // Display SERVICEID in paragraph

        // Clear previous dynamic fields, if any
        $('#dynamicFieldsSection').empty();

        // Create dynamic fields based on fieldname values
        if (serviceData.fieldname) {
            var fieldnames = serviceData.fieldname.split(',');

            if (fieldnames.length > 0) {
                var maxFields = Math.min(fieldnames.length, 7); // Limit to a maximum of 7 fields

                for (var i = 0; i < maxFields; i++) {
                    var inputField = "<div class='mb-4'>" +
                                        "<label for='" + fieldnames[i].toLowerCase().trim() + "' class='block text-sm font-medium text-white-700'>" + fieldnames[i].trim() + "</label>" +
                                        "<input type='text' id='" + fieldnames[i].toLowerCase().trim() + "' name='" + fieldnames[i].toLowerCase().trim() + "' class='form-input w-full py-2 px-3 border border-white-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm' placeholder='Enter ...'> " +
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

    // Handle quantity change event to update cost calculation
    $('#Qnt').on('input', calculateCost);

    // Function to calculate cost based on quantity
    function calculateCost() {
        var baseCost = parseFloat($('#costDisplay').text().replace('$', '')); // Get base cost from displayed text
        var quantity = parseInt($('#Qnt').val()); // Get selected quantity

        if (!isNaN(baseCost) && !isNaN(quantity)) {
            var totalCost = baseCost * quantity; // Calculate total cost
            $('#costDisplay').text('$' + totalCost.toFixed(2)); // Update displayed total cost
            $('#COST').val(totalCost.toFixed(2)); // Update hidden input with calculated COST
        }
    }

    // Handle form submission via AJAX
    $('#serialForm').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        submitForm(); // Call your custom submitForm function
    });

    // Custom function to handle form submission via AJAX
    function submitForm() {
        // Get individual form fields
        var _token = $('#serialForm input[name="_token"]').val();
        var servicename = $('#serialForm input[name="servicename"]').val();
        var COST = $('#COST').val(); // Use the dynamically calculated COST
        var Qnt = $('#serialForm input[name="Qnt"]').val();
        var username = $('#serialForm input[name="username"]').val();
        var SERVICEID = $('#serialForm input[name="SERVICEID"]').val();

        // Prepare serial number data
        var serialNumber = Array.prototype.reduce.call($("#dynamicFieldsSection input[type='text']"), function(acc, input) {
            return acc + `"${$(input).attr('name')}":"${$(input).val()}",`;
        }, "");
        serialNumber = serialNumber.slice(0, -1); // Remove the trailing comma

        // Prepare data object for AJAX
        var formData = {
            _token: _token,
            servicename: servicename,
            COST: COST,
            Qnt: Qnt,
            username: username,
            SERVICEID: SERVICEID,
            SERIAL_NUMBER: serialNumber // Add the concatenated SERIAL_NUMBER field
        };

        // Log formatted data before sending
        console.log('Formatted Data:', formData);

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
