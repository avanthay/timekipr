var APP = APP || {};

/* jQuery selector variables */
APP.references = {
    alertContainer: '#tk-alert-container',
    employeeTable: {
        el: '#tk-employee-table',
        data: {
            employees: 'tkEmployees',
            action: 'tkAction'
        }
    },
    employeeCreate: '#tk-employee-create',
    modalConfirm: '#tk-modal-confirm',
    modalConfirmEmployee: '#tk-modal-confirm-employee',
    modalConfirmButton: '#tk-modal-confirm-button',
    modalEdit: '#tk-modal-edit',
    modalEditLabel: '#tk-modal-edit-label',
    modalEditFirstname: '#tk-modal-edit-firstname',
    modalEditLastname: '#tk-modal-edit-lastname',
    modalEditEmail: '#tk-modal-edit-email',
    modalEditIsTeamLeader: '#tk-modal-edit-isTeamLeader',
    modalEditIsManager: '#tk-modal-edit-isManager',
    modalEditButton: '#tk-modal-edit-button'
};

/**
 * Convenience function to retrieve a jQuery object from the defined reference
 * @param selector  The defined reference
 * @returns jQuery
 */
APP.$ = function(selector) {
    reference = this.references[selector];
    return $((typeof reference == 'string') ? reference : reference.el);
};

/**
 * Convenience function to retrieve the data set in DOM
 * @param reference The defined reference separated with a dot, e.g.: 'selectorName.dataSelectorName'
 * @returns {*} The data object if found
 */
APP.data = function(reference) {
    reference = reference.split('.');
    return this.$(reference[0]).data(this.references[reference[0]].data[reference[1]]);
};

/**
 * Convenience function to concat strings
 * @param {Array} strings
 * @param {string} separator
 * @returns {string}
 */
APP.concat = function(strings, separator) {
    var filterEmptyElements = function(item) {
        return item;
    };
    return strings.filter(filterEmptyElements).join(separator);
};

/**
 * Refresh an already initialized WATable
 * @param table The jQuery table object
 * @param rows  The row data from the table (not the complete configuration data)
 */
APP.refreshWATable = function(table, rows) {
    var tableData;
    var waTable = table.data('WATable');
    if (waTable && typeof waTable == 'object') {
        tableData = waTable.getData();
        tableData.rows = rows;
        waTable.setData(tableData);
    }
};

APP.showMessage = function(message, error) {
    var alertContainer = APP.$('alertContainer');
    var alert = $(alertContainer.data('tk-alert'));
    alert.find('.tk-alert-text').text(message);
    alert.addClass(error ? 'alert-danger' : 'alert-success');
    alertContainer.append(alert);
    setTimeout(function() {
        alert.remove();
    }, 5000);
};

/* initialize the app */
APP.init = function() {
    this.admin.init();
};

/* admin namespace */
APP.admin = {};
APP.admin.init = function() {
    this.employee.init();
};

/* admin.employee namespace */
APP.admin.employee = (function() {
    this.init = function() {
        initializeTable();
        registerListeners();
    };
    var registerListeners = function() {
        APP.$('modalConfirmButton').click(deleteConfirmClicked);
        APP.$('modalEditButton').click(editSaveClicked);
        APP.$('employeeCreate').click(addEmployeeClicked);
    };
    var addEmployeeClicked = function(event) {
        showEditForm();
    };
    var initializeTable = function() {
        var table, data;
        if ((table = APP.$('employeeTable')).length && (data = APP.data('employeeTable.employees'))) {
            table.WATable({
                pageSize: 25,
                pageSizes: [],
                hidePagerOnEmpty: true,
                data: {
                    cols: {
                        id: {
                            type: 'number',
                            unique: true,
                            hidden: true
                        },
                        firstname: {
                            type: 'string',
                            friendly: Translator.trans('firstname')
                        },
                        lastname: {
                            type: 'string',
                            friendly: Translator.trans('lastname'),
                            sortOrder: 'asc'
                        },
                        email: {
                            type: 'string',
                            friendly: Translator.trans('email')
                        },
                        isTeamLeader: {
                            type: 'bool',
                            cls: 'text-center',
                            friendly: Translator.trans('teamLeader')
                        },
                        isManager: {
                            type: 'bool',
                            cls: 'text-center',
                            friendly: Translator.trans('manager')
                        },
                        action: {
                            type: 'string',
                            cls: 'text-center',
                            friendly: Translator.trans('action'),
                            format: APP.data('employeeTable.action')
                        }
                    },
                    rows: data
                },
                rowClicked: tableRowClicked
            });
        }
    };
    var refreshTable = function() {
        action.getEmployees(function(resp) {
            APP.refreshWATable(APP.$('employeeTable'), resp)
        });
    };
    var tableRowClicked = function(data) {
        var employee = data.row;
        var target = $(data.event.target);
        if (target.hasClass('tk-edit')) {
            data.event.preventDefault();
            showEditForm(employee);
        }
        if (target.hasClass('tk-delete')) {
            data.event.preventDefault();
            showDeleteConfirmation(employee);
        }
    };
    var operationConfirmed = function(response) {
        APP.showMessage(response.message || Translator.trans('operationSuccessful'));
        refreshTable();
    };
    var editSaveClicked = function(event) {
        var modalEditEmail = APP.$('modalEditEmail');
        var employee = {};
        employee.id = APP.$('modalEditButton').data('tkEmployeeId');
        employee.firstname = APP.$('modalEditFirstname').val();
        employee.lastname = APP.$('modalEditLastname').val();
        employee.email = modalEditEmail.val();
        employee.isTeamLeader = +APP.$('modalEditIsTeamLeader').prop('checked');
        employee.isManager = +APP.$('modalEditIsManager').prop('checked');
        if (!modalEditEmail[0].checkValidity()) {
            modalEditEmail.closest('.form-group').addClass('has-error');
            return;
        }
        modalEditEmail.closest('.form-group').removeClass('has-error');
        APP.$('modalEdit').modal('hide');
        if (employee.id) {
            action.updateEmployee(employee, operationConfirmed);
            return;
        }
        action.createEmployee(employee, operationConfirmed);
    };
    var showEditForm = function(employee) {
        var label = APP.$('modalEditLabel');
        label.text(employee ? label.data('tkLabelEdit') : label.data('tkLabelCreate'));
        APP.$('modalEditFirstname').val(employee ? employee.firstname : '');
        APP.$('modalEditLastname').val(employee ? employee.lastname : '');
        APP.$('modalEditEmail').val(employee ? employee.email : '');
        APP.$('modalEditIsTeamLeader').prop('checked', employee ? employee.isTeamLeader : false);
        APP.$('modalEditIsManager').prop('checked', employee ? employee.isManager : false);
        APP.$('modalEditButton').data('tkEmployeeId', employee ? employee.id : null);
    };
    var deleteConfirmClicked = function(event) {
        APP.$('modalConfirm').modal('hide');
        var employeeId = APP.$('modalConfirmEmployee').data('tkEmployeeId');
        action.deleteEmployee(employeeId, operationConfirmed);
    };
    var showDeleteConfirmation = function(employee) {
        var employeeString = APP.concat([employee.firstname, employee.lastname, employee.email]);
        APP.$('modalConfirmEmployee').text(employeeString).data('tkEmployeeId', employee.id);
    };
    var action = {
        deleteEmployee: function(employeeId, success) {
            $.ajax({
                url: '/data/employee/' + employeeId,
                type: 'DELETE',
                success: success
            });
        },
        createEmployee: function(employee, success) {
            $.ajax({
                url: '/data/employee',
                type: 'POST',
                data: employee,
                success: success
            })
        },
        updateEmployee: function(employee, success) {
            $.ajax({
                url: '/data/employee/' + employee.id,
                type: 'PUT',
                data: employee,
                success: success
            })
        },
        getEmployees: function(success) {
            $.get('/data/employee', success);
        }
    };
    return this;
})();

/* start the app on document ready */
jQuery(function() {
    APP.init();
});