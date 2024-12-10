$(document).on("click", ".appoinementDetails", function(e) {
    e.preventDefault();

    let appoinementId = $(this).data('uuid');
    
    $.ajax({
        url: APP_URL + `/ajax/get-appoinement-details`,
        type: "GET",
        data: { uuid: appoinementId },
        success: function(response) {
            if (!response || !response.data) {
                console.error("No appointment data received.");
                return;
            }
            
            let appointmentData = response.data;
            let startDatetime = new Date(appointmentData.insp_start_datetime);
            let endDatetime = new Date(appointmentData.insp_end_datetime);
            let appointmentStatus = '';
            
            // Display appointment details in the modal
            $('#listAppoinementDetailsModal').modal('show');
            $('#inspectionId').val(appointmentData.uuid);
            $('#inspectionType').val(appointmentData.inspection_type_id).change();
            $('#insp_customer_name').text(appointmentData.customers.name);
            $('#insp_customer_phone').text(appointmentData.customers.mobile_number);
            $('#insp_customer_time').text(
                startDatetime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) + 
                ' - ' + 
                endDatetime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })
            );
            $('#insp_customer_date').text(startDatetime.toLocaleDateString());

            // Set referral remove UUID
            $('.referral_remove').attr('data-uuid', appointmentData.uuid);

            // Determine appointment status and apply corresponding styles
            switch (appointmentData.is_active) {
                case 0:
                    appointmentStatus = 'Pending';
                    $('.appoyment-status').addClass('chip-pending').text(appointmentStatus);
                    break;
                case 1:
                    appointmentStatus = 'Complete';
                    $('.appoyment-status').addClass('chip-completed').text(appointmentStatus);
                    break;
                case 2:
                    appointmentStatus = 'Cancelled';
                    $('.appoyment-status').addClass('chip-cancelled').text(appointmentStatus);
                    break;
                case 3:
                    appointmentStatus = 'Rescheduled';
                    $('.appoyment-status').addClass('chip-reschedule').text(appointmentStatus);
                    break;
            }

            // Hide irrelevant actions based on appointment status
            if (appointmentData.is_active === 0) {
                alert('Appointment is pending');
            } else {
                alert('Appointment status updated');
            }

            // Toggle visibility of certain elements based on appointment status
            $('.inspection-complete').hide();
            $('.inspection-complete-checkbox').hide();
            $('#referral-remove').hide();
            $('.re-schedule').hide();
            $('.cancel_inspection').hide();
        },
        error: function(xhr) {
            console.error("Error fetching appointment details:", xhr);
        }
    });
});

****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
****************************************************************************
