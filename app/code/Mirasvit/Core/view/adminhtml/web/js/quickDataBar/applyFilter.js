define([
    'uiComponent',
    'ko',
    'underscore',
    'jquery',
    'uiRegistry'
], function (Component, ko, _, $, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            providerName: '',
            filterName:   '',
        },

        initObservable: function () {
            this._super();

            this.loading = ko.observable(this.loading)
            this.data = ko.observable(this.data)

            return this;
        },
        
        handleClick: function (filter) {
            if (!this.providerName || !this.filterName) {
                return false;
            }
            
            var filterBlock = registry.get(this.providerName);
            var filterElem  = registry.get(this.providerName + '.' + this.filterName);
    
    
            if (typeof filterBlock == 'undefined' || typeof filterElem == 'undefined') {
                return false;
            }
            
            filterBlock.clear();
            filterElem.value(filter);
            filterBlock.apply();
            
            return false;
        }
    });
});
