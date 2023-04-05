"use strict";

var PermissionModule = (function() {
    var getAllAdminsData = function() {
        $.get(BASE_URL + 'controller/permissionController.php?action=get-all-admins', function(res) {
            $('#tbl-admins tbody').html(res);
        });
    };
    
    var init = function() {
        getAllAdminsData();
    };

    return {
        init: init
    }
})();

$(document).ready(function() {
    PermissionModule.init();
    // EditableTableModule.init('#tbl-admins');
    const adminColumns = {
        no: {
            type: 'number',
            editable: false
        },
        name: {
            type: 'text',
            editable: true,
        },
        email: {
            type: 'email',
            editable: true,
        },
        password: {
            type: 'text',
            editable: true,
        },
        last_signed_in: {
            type: 'datetime',
            editable: false,
        },
        permission: {
            type: 'checkbox',
            editable: false,
        },
        is_online: {
            type: 'icon',
            editable: false,
        }
    };
    const adminRequest = {
        url: BASE_URL + 'controller/permissionController.php',
        actions: {
            add: 'add-new-admin',
            update: 'update-admin',
            delete: 'delete-admin',
            update_permission: 'update-permission',
        }
    };
    // Create editable table for admin management
    EditableTable.prototype.customEventHandlers = function() {
        const { id, request } = this.state;
        $(id + ' tbody').on('change', '.switch input[type=checkbox]', function() {
            const permission = $(this).is(':checked');
            const row_id = Number.parseInt($(this).attr('data-id'));
            const data = { row_id, permission };
            // Send request to change permission state
            $.post(
                request.url,
                {
                    action: request.actions.update_permission,  
                    data
                },
                function(res) {
                    console.log(res);
                    switch (res) {
                        case 'success':
                            toastr.success('Admin permission has changed.', 'Success!');
                            break;
                        case 'not_exist':
                            toastr.warning('This data row does not exist.', 'Warning!');
                            break;
                        default:
                            toastr.warning('Please try again after reload this page.', 'Warning!');
                            break;
                    }
                }
            );
        });
    };
    const adminTable = new EditableTable('#tbl-admins', adminColumns, adminRequest);
    adminTable.init();
    adminTable.customEventHandlers();
});