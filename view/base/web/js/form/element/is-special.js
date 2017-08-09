define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        selectOption: function(id){
            if(($("#"+id).val() == 0)||($("#"+id).val() == undefined)) {
                $('div[data-index="monday_open_time"]').hide();
                $('div[data-index="monday_close_time"]').hide();
                $('div[data-index="monday_break_time"]').hide();
                $('div[data-index="monday_offbreak_time"]').hide();
            } else if($("#"+id).val() == 1) {
                $('div[data-index="monday_open_time"]').show();
                $('div[data-index="monday_close_time"]').show();
                $('div[data-index="monday_break_time"]').show();
                $('div[data-index="monday_offbreak_time"]').show();
            }
        },
    });
});