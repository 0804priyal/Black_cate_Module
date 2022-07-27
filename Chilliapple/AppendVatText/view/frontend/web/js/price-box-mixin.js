/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'mage/template',
    'jquery/ui'
], function ($, utils, _, mageTemplate) {
    'use strict';
    

    var globalOptions = {
        productId: null,
        priceConfig: null,
        prices: {},
        //priceTemplate: $('body').hasClass('catalog-product-view') ? "<span class='price'><%- data.formatted %><strong> +VAT</strong></span>": "<span class='price'><%- data.formatted %></span>"
        priceTemplate: "<span class='price'><%- data.formatted %><strong> +VAT</strong></span>"
    };

    return function (widget) {

    $.widget('mage.priceBox', widget, {  
        options: globalOptions,   
        _init: function initPriceBox() {
            var box = this.element;

            box.trigger('updatePrice');
            this.cache.displayPrices = utils.deepClone(this.options.prices);
            this._bindEvents();
        },  
        _bindEvents : function(){
            if($('body').hasClass('catalog-product-view')){
                if($.trim($('.price-final_price').html()) == ''){
                    $('.price-final_price').attr('style','display:none !important');
                }
                
                
            }
        }

    });

    return $.mage.priceBox;

    }
});
