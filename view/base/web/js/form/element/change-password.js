define([
    'jquery',
    'Magento_Ui/js/form/element/single-checkbox'
], function ($, SingleCheckbox) {
    'use strict';

    return SingleCheckbox.extend({
        /**
         * @inheritdoc
         */
        onCheckedChanged: function (newChecked) {
            if(newChecked) {
                $('div[data-index="password_hash"]').show();
                $('div[data-index="password_confirmation"]').show();
            } else {
                $('div[data-index="password_hash"]').hide();
                $('div[data-index="password_confirmation"]').hide();
            }
            return this._super(newChecked);
        }
    });
});