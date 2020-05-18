    /*  Checkout Page */
    if (jQuery("body").hasClass("woocommerce-checkout")) {

        

        jQuery('body').prepend('<p class="click-here-login">Login / Register</p>');
        jQuery('.click-here-login').click(function(){
            jQuery(this).hide();
            jQuery('.woocommerce-form-login-toggle').addClass('uncollapsed');
        });

        /* Clone "Payment method" before "Billing / Shipping Address" */
        let paymentMethod = jQuery("#payment_details").clone();
        jQuery(paymentMethod).addClass('cloned-payment-details');
        jQuery(paymentMethod).find('> .collapsed').removeClass('collapsed').addClass('copia');
        /*jQuery('#customer_details').attr("data-ishidden", "true");*/
        
        let changed = false;
        jQuery(paymentMethod).change(function() {

            if(changed == false){
                changed = true; 
                
                /*simulate click on the original payment method list item in order to preserve the "checked"
                class, that visually indicates which credit card is selected with a green border*/
                jQuery('.wc_payment_methods .wc_payment_method').click(function(){
                    let buttonClickedIndex = jQuery(this).index();
                    jQuery('#payment_details:not(.cloned-payment-details) .wc_payment_methods .wc_payment_method img').css('border','none');
                    jQuery('#payment_details:not(.cloned-payment-details) .wc_payment_methods .wc_payment_method').eq(buttonClickedIndex).find('img').css('border','1px solid #008080');
                });
            }
        });

        if(jQuery('#amazon-logout').length){
            // User is logged in with Amazon account.
            // 1. Add class "amazon-user-logged-in" to the body
            // 2. Hide "Delivery Options" with css
            // 3. Show "Place Order" button when user clicks on "Save Shipping Information" button
                
            jQuery('body').addClass("amazon-user-logged-in");
            jQuery('.woocommerce-shipping-fields .button.save-checkout-section').click(function(){
                jQuery('#payment_details:not(.cloned-payment-details) .collapsed').removeClass('collapsed');
            });
        }

        jQuery("#customer_details").before(paymentMethod);

        /*Hide Custom "Payment Method" if user clicked on "Save Shipping Information" */
        jQuery("#customer_details .save-checkout-section").click(function(){
            jQuery("#payment_details .copia").addClass('collapsed');

            /*Show 'edit' button for Cuostom "Payment Method" if any of the following buttons are clicked :*/
            jQuery("#payment_details .edit-checkout-section").removeClass('collapsed');
            jQuery("#payment_details.cloned-payment-details .checkout-section-title-collapsable h4").addClass('checkout_valid_step');
        });


        window.setInterval(function(){
            /*Remove Credit Card payment_box from cloned version, in order to prevent bugs when user is filling data in the original Credit Card payment box */
            /*
            jQuery('.cloned-payment-details .payment_box.payment_method_authorize_net_cim_credit_card').remove();
            jQuery('.cloned-payment-details .place-order').remove();
            */

            /*
            jQuery('.wc_payment_methods .wc_payment_method').each(function(){
                jQuery(this).unbind().bind("click", function(){
                    console.log('clicked');
                    let buttonClickedIndex = jQuery(this).index();
                    jQuery('#payment_details:not(.cloned-payment-details) .wc_payment_methods .wc_payment_method img').css('border','none');
                    jQuery('#payment_details:not(.cloned-payment-details) .wc_payment_methods .wc_payment_method').eq(buttonClickedIndex).find('img').css('border','1px solid #008080');
                });
            });
            */
           

        }, 3000);



        /*Automatic scroll the user to the invalid inputs on billing shipping address fields, and add invalid styles to required inputs with empty value*/
        jQuery('.save-checkout-section').click(function(){
            addInputValidationStyles();
            scrollRequired();
        });

        function scrollRequired(){
            var el = jQuery('.woocommerce-billing-fields .woocommerce-invalid');

            /*Check if 'woocommerce-invalid' class is added. If not, add it.*/
            jQuery(el).addClass('woocommerce-invalid woocommerce-invalid-required-field');

            var elFirst = jQuery(el[0]);
            jQuery('html, body').animate({scrollTop: elFirst.offset().top}, 800);
        }

        function addInputValidationStyles() {
            /*Add 'woocommerce-invalid' class to all the p.validate-required that have empty inputs*/
            var validateRequiredFields = jQuery('.woocommerce-billing-fields .validate-required');
            jQuery(validateRequiredFields).each(function() {
                if(jQuery(this).find('input').length && jQuery(this).find('input').val() == '') {
                    /* Input is empty and is required - add class 'woocommerce-invalid' */  
                    jQuery(this).addClass('woocommerce-invalid');
                }
            });
        }

        jQuery('.button-place-order-copy').click(function(){
            jQuery('#payment_details:not(.cloned-payment-details) > .collapsable').removeClass('collapsed');
        });

    }