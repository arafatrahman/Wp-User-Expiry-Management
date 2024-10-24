jQuery(document).ready(function ($) {
    // Event listener for edit expiry button
    $(document).on('click', '.edit-expiry', function (e) {
        e.preventDefault();

        const userId = $(this).data('user-id');
        const currentExpiry = $('#expiry-date-' + userId).text().trim(); // Trim to remove any leading/trailing spaces

        
        // Populate the input with the current expiry date (if available)
        if (currentExpiry && currentExpiry !== 'Never') {

           

            $('#user_expiry_date_field').val(currentExpiry);

        } else {
           
            // If no expiry date, you might want to clear the input
            $('#user_expiry_date').val('');
        }

        // Show the modal and store user ID in the data attribute
        $('#expiry-date-modal').show().data('user-id', userId);
    });

    // Cancel button to hide the modal
    $('#expiry-date-cancel').on('click', function () {
        $('#expiry-date-modal').hide();
    });

    // Handle form submission (AJAX)
   

        $('#expiry-date-save').click(function(e){    


        // Retrieve user ID and new expiry date
        const userId = $('#expiry-date-modal').data('user-id');
        let newExpiryDate = $('#user_expiry_date_field').val();
       
        // Convert the ISO 8601 format (YYYY-MM-DDTHH:MM) to YYYY-MM-DD HH:MM:SS
        if (newExpiryDate) {
            const date = new Date(newExpiryDate);
            const formattedExpiryDate = date.getFullYear() + '-' +
                ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                ('0' + date.getDate()).slice(-2) + ' ' +
                ('0' + date.getHours()).slice(-2) + ':' +
                ('0' + date.getMinutes()).slice(-2) + ':00';

            newExpiryDate = formattedExpiryDate;
        }

        // Send the AJAX request
        $.ajax({
            url: ajaxurl, // Use WordPress's AJAX URL
            type: 'POST',
            data: {
                action: 'update_user_expiry_date', // Your AJAX action name
                user_id: userId,
                expiry_date: newExpiryDate,
            },
            
            success: function (response) {
                
                
                // Handle success response
                if (response.success) {
                    $('#expiry-date-' + userId).text();
                    $('#expiry-date-' + userId).text(newExpiryDate);
                    $('#expiry-date-modal').hide();
                    location.reload();
                    
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred. Please try again.');
            }
            
        });
        


    });
});
