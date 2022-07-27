define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'Magento_Catalog/js/product/storage/storage-service'
], function ($,Component,ko,$t,modal,_,priceUtils,storage) {
    'use strict';

    return Component.extend({
        options : {
            qty : ko.observable($('#qty').val()),
            gbc : '#require-gold-blocking-container',
            am : '#alert-popup-modal',
            pp : '.goldblocking-form-popup',
            rgb : '#require-gold-blocking',
            gbst: '#product-gold-blocking-subtotal',
            gbe : '#product-gold-blocking-edit',
            gbm : '#product-gold-blocking-minimum',
            s_a_l1 :'#sectionA_line1',
            s_a_l2 :'#sectionA_line2',
            s_a_l3 :'#sectionA_line3',
            s_a  : [],
            facn : 'new',
            fasn : 'save_new',
            faso : 'save_old',
            sectionAPrice : 0.75,
            sectionBPrice : 0.85,
            sectionCPrice : 0.75,
            diePrice :80,
            goldBlockingChanged : false,
            goldBlockingEditMode : false,
            goldBlockingPreviousStatus : false,
            requireLogoOrCrest : false
        },      
        goldBlockKey : $("input[name=product]").val()+'_goldblock',
        productDataStorage : storage.getStorage('product_data_storage'),
        priceForCustomDie : ko.observable(0),
        sectionATotal :ko.observable(0),
        sectionBTotal :ko.observable(0),
        sectionCTotal :ko.observable(0),
        logoFilesTotal :ko.observable(0),
        editedSubTotal : ko.observable(0),
        goldBlockSubTotal : ko.observable(0),
        showEditGoldBlock : ko.observable(false),
        goldBlockSubTotalFormatted : ko.observable(0),
        isRequireGold : ko.observable(false),
        goldBlockData : ko.observableArray([]),
        requireLogoOrCrest : ko.observable(false),
        initialize: function () {
            var o=this,loadFirstTime = true;
            this._super();

            
            if(Object.keys(this.existingQuote).length > 0){
                this.editGoldBlockData(this.existingQuote);    
            }
            
           

            this.options.s_a = [
                this.options.s_a_l1,
                this.options.s_a_l2,
                this.options.s_a_l3
            ];
            if(o.options.qty() < 1){
                o.options.qty(1);
            }
            
            if(Object.keys(o.existingQuote).length > 0){ 
               o.options.qty(o.existingQuote.qty);
               $('#qty').val(o.options.qty());
               
               $('input[type="file"]').each(function(e){
                  
                   var fileClass = $(this).attr('name');
                   var fileName = $('.'+fileClass+'_name').text();
                  
                   if(fileName ==''){
                       return;
                    }
                     if($(this).hasClass('sectionC')){ // price 80
                        o.logoFilesTotal(o.logoFilesTotal()+o.options.diePrice+o.options.sectionCPrice);
                     }else{
                        o.logoFilesTotal(o.logoFilesTotal()+o.options.sectionBPrice);
                     }

               });
               o.priceForCustomDie(o.logoFilesTotal());
                //$('#updated_goldblock_dieprice').val(o.logoFilesTotal()); // for update goldblock
               o.editedSubTotal(o.existingQuote.gold_blocking_subtotal);                       
            }
            
            o.goldBlockSubTotal = ko.computed(function(){
                if(Object.keys(o.existingQuote).length > 0 && loadFirstTime){                     
                    
                    loadFirstTime = false;
                    // divide the subtotal by qty because we need per item subtotal and then we multiply it by qty observer to add dependencies
                    o.editedSubTotal(o.editedSubTotal() / o.options.qty());
                    
                    console.log('firstTime',o.editedSubTotal(),o.sectionATotal(),o.sectionBTotal(),o.sectionCTotal());

                    return (
                            parseFloat(o.editedSubTotal())+
                            parseFloat(o.sectionATotal())+
                            parseFloat(o.sectionBTotal())+
                            parseFloat(o.sectionCTotal())                           
                        ) * parseFloat(o.options.qty());

                }else{
                    o.updateSubtotalSecA(); // to update section A total
                    
                    var excludedSectonC =0,totalPrice = (
                        o.sectionATotal()+
                        o.sectionBTotal()+
                        o.sectionCTotal()+
                        o.logoFilesTotal()
                    ) * o.options.qty();

                    if(o.options.qty() <=1){
                        return totalPrice;
                    }
                    
                   

                    excludedSectonC = (o.sectionATotal()+ o.sectionBTotal()+o.logoFilesTotal()) * o.options.qty();

                    if(o.logoFilesTotal() >=o.options.diePrice){
                        
                       

                        var multiplyPrice = o.logoFilesTotal() -o.options.diePrice;

                        excludedSectonC = (o.sectionATotal()+ o.sectionBTotal()+multiplyPrice) * o.options.qty();

                        console.log(o.logoFilesTotal(),o.sectionATotal(),o.sectionBTotal(),multiplyPrice,excludedSectonC);

                        excludedSectonC = excludedSectonC+o.options.diePrice;
                    }
                    
                    
                   
                    return excludedSectonC;                                
                }
                
            },this);

           
            o.goldBlockSubTotalFormatted = ko.computed(function(){            
                return priceUtils.formatPrice(o.goldBlockSubTotal());
            },this);          
            
            o.bindEvents();
            
            return this;
        },
        deleteExistingLogo : function(data, $el){
            var o= this,elProp;
            elProp = $('#'+$el.target.id).prop('checked');
            // remove price if checked
            if(elProp){
                if($('#'+$el.target.id).hasClass('sectionB')){
                    
                    o.logoFilesTotal(o.logoFilesTotal()-o.options.sectionBPrice);
                }else{
                    o.logoFilesTotal(o.logoFilesTotal()-o.options.diePrice - o.options.sectionCPrice);
                }

            }else{ //add price if not checked
                if($('#'+$el.target.id).hasClass('sectionB')){
                    
                    o.logoFilesTotal(o.logoFilesTotal()+o.options.sectionBPrice);
                }else{
                    o.logoFilesTotal(o.logoFilesTotal()+o.options.diePrice+o.options.sectionCPrice);
                }

            }
            return true;
        },
        bindEvents: function(){
            var self = this;
            $('body').on('keyup','#qty',function(e){  
                self.options.qty($(this).val());
               
            });
        },
        getValue : function(e){
           var self = this;
           if(Object.keys(self.existingQuote).length > 0){
            if(self.existingQuote[$(e).attr('name')] !='undefined'){
                return self.existingQuote[$(e).attr('name')]             
                }        
                return "";
            }
        },
        setSelected : function(e,en){
            
            var self= this;
           
            if(Object.keys(self.existingQuote).length > 0){
               


                if(self.existingQuote[en] !='undefined'){
                    $.each(self.existingQuote[en], function( key, value ) {                   
                      
                       /*******For radio buttons *****/
                        var s_n = en+'['+key+']';                           
                        
                        if($(e).attr('name') == s_n && $(e).val() == value){   
                            $(e).prop('checked',true);                      
                            return true;
                        }else{
                            $(e).removeAttr('checked');
                        }
                        /*******For radio buttons *****/
                        
                    });
                }
            }
            
        },
        editGoldBlockData : function(ed){
            var self= this,modalOptions;            
           // if(typeof this.existingQuote == 'object'){         
            if(Object.keys(this.existingQuote).length > 0){       
                
                $.each( ed.sectionA, function( key, value ) {
                    $('#sectionA_'+key).val(value);
                });                
                $(self.options.rgb).prop("checked",true);
                self.showEditGoldBlock(true);
                self.goldBlockSubTotal(ed.gold_blocking_subtotal);
                self.goldBlockSubTotalFormatted(priceUtils.formatPrice(self.goldBlockSubTotal()));               
               
            }            
        },
        modalOptions : function(){
            var self = this,options = self.options;
            return  {
                type: 'popup',
                responsive: true,
                modalClass : 'goldblocking',
                innerScroll : true,
                appendTo  : self.options.gbc,
                buttons: [
                    {
                        text: $t('Apply Gold-Blocking'),
                        class: '',
                        click: function () {
                            self.applyGoldBlocking();
                        }
                    },
                    {
                    text: $t('Cancel'),
                    class: '',
                        click: function () {
                           if(Object.keys(self.existingQuote).length == 0){
                                $(options.rgb).prop("checked",false);
                           }
                            this.closeModal();
                        }
                    },

                ]
            };
        },
        showpopup : function(){
            var self = this,
                modalOptions,
                popupHtml,
                popup;
            modalOptions = self.modalOptions();
            popup = modal(modalOptions, $(self.options.pp));
            popup.openModal();
            return true;
        },
        applyGoldBlocking : function(){
            var total,that = this;
            //that.isRequireGold(true);
            total = that.updateRequireGoldBlockingPanel();
            
            that.hideGoldBlockingPopup();
            if (total>0 && (that.options.goldBlockingChanged || !that.options.goldBlockingPreviousStatus)) {
                that.options.goldBlockingPreviousStatus = true;
                that.options.goldBlockingChanged = false;
                that.goldBlockingChangedMessage();
            }
        },
        updateRequireGoldBlockingPanel : function(){
            var t, self=this;
            t = self.updateGoldBlockingPrice();                   
            
            /*
            if(t > 0 && t<=10){                    
                self.removeGoldBlocking();
                return t;
            } */           
            self.showEditGoldBlock(true);
            
            
            
            /*if (t < 10) {
                $(self.options.gbm).show();
            } else {
                $(self.options.gbm).hide();
            } */
            return t;
        },
        hideGoldBlockingPopup : function(){
            var self=this;
            $(self.options.pp).modal('closeModal');
        },
        goldBlockingChangedMessage : function(){

        },
        removeGoldBlocking : function(){
            var self = this,options = self.options;
           /* $(options.gbst).hide();
            $(options.gbm).hide();
            $(options.gbe).hide();
            $(options.rgb).prop("checked",false);*/
           // self.isRequireGold(false);
            self.showEditGoldBlock(false);

            if (options.goldBlockingPreviousStatus) {
                options.goldBlockingPreviousStatus = false;
                self.goldBlockingChangedMessage();
            }
        },
        updateSubtotalSecA : function(e,t){
            var that=this,s_data={};
            that.sectionATotal(0); // everytime set start value and then calculate;

            _.each(that.options.s_a, function(section) {
                if($.trim($(section).val()).length>0){
                   var name = $(section).attr('name');
                   var  value = $(section).val();
                    s_data[name] = value;
                    that.sectionATotal(that.sectionATotal()+ that.options.sectionAPrice);
                }
            });

            
        },
        updateSubtotalSecB : function(e,t){
            var self=this,s_b_data={},s_b_action = "#sectionB_file_action",fileBAction = $.trim($(s_b_action).val());

            var name = $(s_b_action).attr('name');
            var value = fileBAction;
            s_b_data[name] = value;

            if (fileBAction==self.options.facn){
                if (fileBAction.length>0) {                   

                    self.sectionBTotal(self.sectionBTotal()+ self.options.sectionBPrice);
                }
            }else if (fileBAction == self.options.fasn || fileBAction == self.options.faso){
                self.sectionBTotal(self.sectionBTotal()+ self.options.sectionBPrice);
            }

            self.goldBlockData(s_b_data);
        },
        updateSubtotalSecC : function(e,t){
            var self=this,fileCAction = $.trim($("#sectionC_file_action").val()),
                sectionCFile = $.trim($("#sectionC_file").val());

            if (fileCAction==self.options.facn){
                if (sectionCFile.length > 0) {
                    self.sectionCTotal(self.sectionCTotal()+self.options.sectionCPrice);
                    self.options.requireLogoOrCrest = true;
                }
            }else if (fileCAction==self.options.fasn || fileCAction==self.options.faso){
                self.sectionCTotal(self.sectionCTotal()+self.options.sectionCPrice);
                self.options.requireLogoOrCrest = true;
            }


        },
        editGoldBlocking : function(){
            
            var self=this,modalOptions,popup;

            modalOptions = self.modalOptions();            
            popup = modal(modalOptions, $(self.options.pp));
           
            popup.openModal();
           
            self.updateSubtotalSecA();
            self.updateSubtotalSecB();
            self.updateSubtotalSecC();       
            //$(self.options.pp).modal('openModal');
        },
        logoUpload : function($el){
            var that = this,$files,elID=$($el).attr('id');
            $files = $el.files[0];
            if($('.'+elID+'_name').length > 0 && $('.'+elID+'_name').text()!=''){
                return;

            }
            


            if(typeof $files == 'undefined'){
                if(that.logoFilesTotal() > 0){
                    if($($el).hasClass('sectionB')){
                       
                        that.logoFilesTotal(that.logoFilesTotal()-that.options.sectionBPrice);
                    }
                    if($($el).hasClass('sectionC')){
                        that.logoFilesTotal(that.logoFilesTotal()-that.options.diePrice - that.options.sectionCPrice);
                        that.requireLogoOrCrest(false);
                    }  
                }else{
                    that.logoFilesTotal(0);
                }
                
                return;
            }
            if($files.name!=''){
                
                if($($el).hasClass('sectionB')){
                   
                    that.logoFilesTotal(that.logoFilesTotal()+that.options.sectionBPrice);
                }
                if($($el).hasClass('sectionC')){
                    that.logoFilesTotal(that.logoFilesTotal()+that.options.diePrice + that.options.sectionCPrice);
                    that.requireLogoOrCrest(true);
                }    
            }
                
        },
        alreadyHaveLogoSection : function(data, $el){
            
            var that= this,elProp;
            elProp = $('#'+$el.target.id).prop('checked');

           if(Object.keys(that.existingQuote).length > 0){
                if(elProp){
                    that.editedSubTotal(that.editedSubTotal() - that.options.diePrice -that.options.sectionCPrice);
                    
                }else{
                    that.editedSubTotal(that.editedSubTotal()+that.options.diePrice+(that.options.sectionCPrice));
                    
                }

            }else{
                if(elProp){
                    that.logoFilesTotal(that.logoFilesTotal() - that.options.diePrice - that.options.sectionCPrice );
                    
                }else{
                    that.logoFilesTotal(that.logoFilesTotal()+that.options.diePrice+that.options.sectionCPrice);
                    
                }

            }
            
            
          
            return true;
        },
        updateGoldBlockingPrice : function(){
            var self=this,storageObj;
            self.updateSubtotalSecA();
            self.updateSubtotalSecB();
            self.updateSubtotalSecC();
                 

            //Section C, die price
            if (self.requireLogoOrCrest() && $("#sectionC_alreadyHaveLogo").prop("checked")==false) {
                self.priceForCustomDie(self.options.diePrice);
            }
            return (parseFloat(self.goldBlockSubTotal()) + parseFloat(self.priceForCustomDie())+self.options.sectionCPrice);
        },

        storeFormData : function(){
            var data = {},that=this;
            
            $("#product_addtocart_form").serializeArray().map(function(x){
                data[x.name] = x.value;
            });
            that.goldBlockData(data)            
        },
        

    });
});
