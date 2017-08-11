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
        initialize: function () {
            this._super();

            var source = registry.get(this.provider);

            if(!source.data.customer.entity_id) {
                this.setVisible(false);
                registry.get(this.password, function (passwordInput) {
                    passwordInput.setVisible(false);
                });
                registry.get(this.passwordConfirmation, function (passwordConfirmationInput) {
                    passwordConfirmationInput.setVisible(false);
                });
            }

            return this;
        },
        /**
         * @inheritdoc
         */
        onCheckedChanged: function (newChecked) {
            registry.get(this.password, function (passwordInput) {
                passwordInput.setVisible(newChecked);
            });
            registry.get(this.passwordConfirmation, function (passwordConfirmationInput) {
                passwordConfirmationInput.setVisible(newChecked);
            });
            return this._super(newChecked);
        }
    });
});