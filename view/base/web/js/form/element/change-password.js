define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox'
], function ($, registry, SingleCheckbox) {
    'use strict';

    return SingleCheckbox.extend({
        defaults: {
            password: '${ $.parentName }.password_hash',
            passwordConfirmation: '${ $.parentName }.password_confirmation'
        },
        /**
         * @inheritdoc
         */
        onCheckedChanged: function (newChecked) {
            registry.get(this.password, function (passwordInput) {
                passwordInput.setVisible(newChecked);
            });
            registry.get(this.passwordConfirmation, function (input) {
                input.setVisible(newChecked);
            });
            return this._super(newChecked);
        }
    });
});