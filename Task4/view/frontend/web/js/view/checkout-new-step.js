define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/customer',
        'mage/storage',
    ],
    function (
        ko,
        Component,
        _,
        quote,
        stepNavigator,
        resourceUrlManager,
        errorProcessor,
        fullScreenLoader,
        customer,
        storage
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Speroteck_Task4/checkout-new-step'
            },

            checkoutFields: {
                "delivery-date": { label: "Delivery Date4.1", element: null },
                "certificate-value": { label: "Certificate Value4.1", element: null },
            },

            deliveryDate: ko.observable(),
            isVisible: ko.observable(true),
            isLogedIn: customer.isLoggedIn(),

            stepCode: 'deliveryDate',
            stepTitle: 'Delivery Date',

            initialize: function () {
                this._super();

                stepNavigator.registerStep(
                    this.stepCode,
                    null,
                    this.stepTitle,
                    this.isVisible,
                    _.bind(this.navigate, this),
                    15
                );

                return this;
            },

            initElement: function (elem) {
                for (let key in this.checkoutFields) {
                    if (elem.name.indexOf(key) >= 0) {
                        this.checkoutFields[key].element = elem;
                    }
                }
                return this;
            },

            navigate: function () {

            },

             navigateToNextStep: function () {
                if (!this.elems()[0].value() && !this.elems()[1].value()) {
                    return stepNavigator.next();
                }

                fullScreenLoader.startLoader();
                var payload = {
                    delivery_date: this.elems()[0].value(),
                    certificate_value: this.elems()[1].value()
                };

                return storage.post(
                    'rest/V1/setDeliveryDate',
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        fullScreenLoader.stopLoader();
                        if (response) {
                            stepNavigator.next();
                        }
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );
            }
        });
    }
);
