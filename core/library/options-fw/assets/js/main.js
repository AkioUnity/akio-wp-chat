(function($, document) {

    var lcx = {

        cache: function() {
            lcx.els = {};
            lcx.vars = {};

            lcx.els.tab_links = $('.lcx-tab-link');
        },

        on_ready: function() {

            // on ready stuff here
            lcx.cache();
            lcx.trigger_dynamic_fields();
            lcx.setup_groups();
            lcx.setup_tabs();

        },

        /**
         * Trigger dynamic fields
         */
        trigger_dynamic_fields: function() {

            lcx.setup_timepickers();
            lcx.setup_datepickers();

        },

        /**
         * Setup the main tabs for the settings page
         */
        setup_tabs: function() {

            lcx.els.tab_links.on('click', function(){

                // Set tab link active class
                lcx.els.tab_links.removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                // Show tab
                var tab_id = $(this).attr('href');

                $('.lcx-tab').removeClass('lcx-tab--active');
                $(tab_id).addClass('lcx-tab--active');

                return false;

            });

        },

        /**
         * Set up timepickers
         */
        setup_timepickers: function() {

            $('.timepicker').not('.hasTimepicker').each(function(){

                var timepicker_args = $(this).data('timepicker');

                $(this).timepicker( timepicker_args );

            });

        },

        /**
         * Set up timepickers
         */
        setup_datepickers: function() {

            $('.datepicker').not('.hasTimepicker').each(function(){

                var datepicker_args = $(this).data('datepicker');

                $(this).datepicker( datepicker_args );

            });

        },

        /**
         * Setup repeatable groups
         */
        setup_groups: function() {

            // add row

            $(document).on('click', '.lcx-group__row-add', function(){

                var $group = $(this).closest('.lcx-group'),
                    $row = $(this).closest('.lcx-group__row'),
                    template_name = $(this).data('template'),
                    $template = $('#'+template_name).html();

                $row.after( $template );

                lcx.reindex_group( $group );

                lcx.trigger_dynamic_fields();

                return false;

            });

            // remove row

            $(document).on('click', '.lcx-group__row-remove', function(){

                var $group = jQuery(this).closest('.lcx-group'),
                    $row = jQuery(this).closest('.lcx-group__row');

                $row.remove();

                lcx.reindex_group( $group );

                return false;

            });

        },

        /**
         * Reindex a group of repeatable rows
         *
         * @param arr $group
         */
        reindex_group: function( $group ) {

            if( $group.find(".lcx-group__row").length == 1 ) {
                $group.find(".lcx-group__row-remove").hide();
            } else {
                $group.find(".lcx-group__row-remove").show();
            }

            $group.find(".lcx-group__row").each(function(index) {

                $(this).removeClass('alternate');

                if(index%2 == 0)
                    $(this).addClass('alternate');

                $(this).find("input").each(function() {
                    var name = jQuery(this).attr('name'),
                        id = jQuery(this).attr('id');

                    if(typeof name !== typeof undefined && name !== false)
                        $(this).attr('name', name.replace(/\[\d+\]/, '['+index+']'));

                    if(typeof id !== typeof undefined && id !== false)
                        $(this).attr('id', id.replace(/\_\d+\_/, '_'+index+'_'));

                });

                $(this).find('.lcx-group__row-index span').html( index );

            });

        }

    };

	$(document).ready( lcx.on_ready() );

}(jQuery, document));
