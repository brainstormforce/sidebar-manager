;(function ( $, window, undefined ) {

	var user_role_update_close_button = function(wrapper) {

		type 		= wrapper.closest('.bsf-sb-user-role-wrapper').attr('data-type');
		rules 		= wrapper.find('.bsf-sb-user-role-condition');
		show_close	= false;

		if ( rules.length > 1 ) {
			show_close = true;
		}

		rules.each(function() {
			if ( show_close ) {
				jQuery(this).find('.user_role-condition-delete').removeClass('bsf-sb-hidden');
			}else{
				jQuery(this).find('.user_role-condition-delete').addClass('bsf-sb-hidden');
			}
		});
	};

	$(document).ready(function($) {

		jQuery('.bsf-sb-user-role-selector-wrapper').each(function() {
			user_role_update_close_button( jQuery(this) );
		})
		
		jQuery( '.bsf-sb-user-role-selector-wrapper' ).on( 'click', '.user_role-add-rule-wrap a', function(e) {
			e.preventDefault();
			e.stopPropagation();
			var $this 		= jQuery( this ),
				id 			= $this.attr( 'data-rule-id' ),
				new_id 		= parseInt(id) + 1,
				rule_wrap 	= $this.closest('.bsf-sb-user-role-selector-wrapper').find('.user_role-builder-wrap'),
				template  	= wp.template( 'bsf-sb-user-role-condition' ),
				field_wrap 	= $this.closest('.bsf-sb-user-role-wrapper');

			rule_wrap.append( template( { id : new_id } ) );
			
			$this.attr( 'data-rule-id', new_id );

			user_role_update_close_button( field_wrap );
		});

		jQuery( '.bsf-sb-user-role-selector-wrapper' ).on( 'click', '.user_role-condition-delete', function(e) {
			var $this 			= jQuery( this ),
				rule_condition 	= $this.closest('.bsf-sb-user-role-condition'),
				field_wrap 		= $this.closest('.bsf-sb-user-role-wrapper');
				cnt 			= 0,
				data_type 		= field_wrap.attr( 'data-type' ),
				optionVal 		= $this.siblings('.user_role-condition-wrap').children('.user_role-condition').val();

			rule_condition.remove();

			field_wrap.find('.bsf-sb-user-role-condition').each(function(i) {
				var condition       = jQuery( this ),
					old_rule_id     = condition.attr('data-rule'),
					select_location = condition.find('.user_role-condition'),
					location_name   = select_location.attr( 'name' );
					
				condition.attr( 'data-rule', i );

				select_location.attr( 'name', location_name.replace('['+old_rule_id+']', '['+i+']') );

				condition.removeClass('bsf-sb-user-role-'+old_rule_id).addClass('bsf-sb-user-role-'+i);

				cnt = i;
			});

			field_wrap.find('.user_role-add-rule-wrap a').attr( 'data-rule-id', cnt )

			user_role_update_close_button( field_wrap );
		});
	});
}(jQuery, window));