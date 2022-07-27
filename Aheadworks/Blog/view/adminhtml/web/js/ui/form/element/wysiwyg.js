/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_PageBuilder/js/form/element/wysiwyg',
    'Magento_PageBuilder/js/events',
    'underscore'
], function (Wysiwyg, events, _) {
    'use strict';

    return Wysiwyg.extend({
        defaults: {
            listens: {
                '${ $.provider }:component.loaded': 'initPageBuilder',
            }
        },

        /**
         * {@inheritDoc}
         */
        initPageBuilder: function () {
            var renderedIds = this.source.renderedBuilderIds ? this.source.renderedBuilderIds : [];

            if (!this.source.isLocked) {
                this._super();
                if (!_.contains(renderedIds, this.pageBuilder.id)) {
                    this._initEvents(this.pageBuilder.id);
                    renderedIds.push(this.pageBuilder.id);
                    this.source.renderedBuilderIds = renderedIds;
                    this.source.isLocked = true;
                }
            }
        },

        /**
         * Initialize events
         *
         * @param {String} id
         * @private
         */
        _initEvents: function (id) {
            events.on('stage:' + id + ':masterFormatRenderAfter', function (args) {
                this.source.isLocked = false;
                this.source.trigger('component.loaded');
            }.bind(this));
        }
    });
});
