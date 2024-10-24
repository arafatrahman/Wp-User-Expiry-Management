<!-- views/modal-view.php -->

<!-- Modal HTML -->
<div id="expiry-date-modal" style="display: none;">
    <div class="expiry-date-modal-overlay"></div>
    <div class="expiry-date-modal-content">
        <h2>Set Expiry Date</h2>
        <form id="expiry-date-form"  method="POST">
       
            <div class="form-group">
                <label for="user_expiry_date">Expiry Date and Time:</label>
                <input type="datetime-local" id="user_expiry_date_field" name="user_expiry_date">
            </div>
            
            <div class="modal-buttons">
                <button type="button" id="expiry-date-save" class="save-btn">Save</button>
                <button type="button" id="expiry-date-cancel" class="cancel-btn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal CSS -->
<style>
    /* Modal Overlay */
    .expiry-date-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    /* Modal Container */
    #expiry-date-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    /* Modal Content Box */
    .expiry-date-modal-content {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
        z-index: 1001;
    }

    /* Form Styles */
    #expiry-date-form {
        display: flex;
        flex-direction: column;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-group input {
        width: 100%;
        padding: 8px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    /* Button Styles */
    .modal-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .save-btn,
    .cancel-btn {
        padding: 10px 15px;
        font-size: 14px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .save-btn {
        background-color: #28a745;
        color: #ffffff;
    }

    .cancel-btn {
        background-color: #dc3545;
        color: #ffffff;
    }

    .save-btn:hover {
        background-color: #218838;
    }

    .cancel-btn:hover {
        background-color: #c82333;
    }
</style>
