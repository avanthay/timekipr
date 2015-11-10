var APP = APP || {};

/* jQuery selector variables */
APP.references = {
    employeeTable: {
        el: '#tk-employee-table',
        data: {
            employees: 'tkEmployees',
            action: 'tkAction'
        }
    },
    somethingElse: '.somethingElse'
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

APP.initializeEmployeeTable = function() {
    var table, data;
    if ((table = this.$('employeeTable')).length && (data = this.data('employeeTable.employees'))) {
        globalTable = table.WATable({
            pageSize: 25,
            pageSizes: [],
            hidePagerOnEmpty: true,
            data: {
                /* todo set correct table proprieties */
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
                        format: this.data('employeeTable.action')
                    }
                },
                rows: data
            }
        });
    }
};


APP.init = function() {
    this.initializeEmployeeTable();
    //todo;
};

/* start the app on document ready */
jQuery(function() {
    APP.init();
});