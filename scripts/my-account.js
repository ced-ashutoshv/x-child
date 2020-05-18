(function( $ ) {
	'use strict';

 	$(function() {
 		let beforeSend = function(){
			$( '.woocommerce-EditAccountForm').addClass( 'processing' );
			$( '.woocommerce-EditAccountForm').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		}
		let afterSend = function(){
			$( '.woocommerce-EditAccountForm').removeClass( 'processing' );
			$( '.woocommerce-EditAccountForm').unblock();
		}
		$( document ).on( 'click', '.reset-Button.button', function(e) {
			e.preventDefault();
			var btn = $(this);
			if(btn.hasClass('disabled')){
				return;
			}
			
			$.ajax({
				url : my_account_object.ajax_url,
				type : 'post',
				data : {
					action : 'yv_reset_password',
					_ajax_nonce : my_account_object.nonce_reset_pw
				},
				beforeSend: function(){
					btn.css('opacity',0.5);
					btn.css('pointer-events','none');
					btn.addClass('disabled');
					beforeSend();
					
				},
				success : function( xhr, textStatus  ) {
					$('.reset_email').slideDown(500);
					btn.hide(100);
					afterSend();
				}, 
				error : function( xhr, textStatus, errorThrown ){
					if( xhr.responseJSON ){
		                message =  xhr.responseJSON.data.message;
		            }else{
		                message = 'We were unable to reset your password, please reload the page and try again';
		            }
		            btn.css('opacity',1);
					btn.css('pointer-events','initial');
					btn.removeClass('disabled');
					btn.show(100);
					$('.reset_email').html(message).slideDown(100);
					afterSend();
				}
			});
		})

		$( document ).on( 'click', '.edit-account .woocommerce-Button.button', function(e) {

			var btn = $(this);
			if(btn.hasClass('disabled')){
				return false;
			}
			//set the first name and last name from full name field
			var name = $('#account_display_name').val();
			var email= $('#account_email').val();

			if( !name || !email ){
				return true;
			}else{
				e.preventDefault();	
			}
			var email_error='';
			var array_name=name.split(" ");
			var first_name=array_name[0];
			var last_name=array_name[1];
			
			$('#account_first_name').val(first_name);
			$('#account_last_name').val(last_name);

			$.ajax({
				url : my_account_object.ajax_url,
				type : 'post',
				data : {
					action : 'yv_update_profile',
					_ajax_nonce : my_account_object.nonce_update_profile,
					fullName : name,
					email : email,
				},
				beforeSend: function(){
					btn.css('opacity',0.5);
					btn.css('pointer-events','none');
					btn.addClass('disabled');
					beforeSend();
				},
				success : function( xhr, textStatus  ) {
					$('form.edit-account').submit();

				}, 
				error : function( xhr, textStatus, errorThrown ){
					if( xhr.responseJSON ){
		                message =  xhr.responseJSON.data.message;
		            }else{
		                message = 'We were unable to update your profile, please reload the page and try again';
		            }
		            btn.css('opacity',1);
					btn.css('pointer-events','initial');
					btn.removeClass('disabled');
					$('.social_notice').html(message).slideDown(100);
					afterSend();
				}
			});
		})
	});
	 

})( jQuery );
