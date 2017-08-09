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
                $('div[data-index="password_hash"]').hide();
                $('div[data-index="password_confirmation"]').hide();
            } else if($("#"+id).val() == 1) {
                $('div[data-index="password_hash"]').show();
                $('div[data-index="password_confirmation"]').show();
            }
        },
    });
});