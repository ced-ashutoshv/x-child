jQuery(document).ready(function() {
    cartCustomization();
});
jQuery( document.body ).on( 'updated_cart_totals', function(){
    cartCustomization();
});

function cartCustomization() {
    /*Functionality to add/remove product items*/
    /*We created custom +/- inputs, that when pressed, simulate a click on the original hidden +/- inputs (that are not stylized)*/
    jQuery('.product-quantity .plus-icon').unbind().bind("click", function() {
        var currentQuantity = jQuery(this).closest('.product-quantity').find('.quantity .qty').val();
        jQuery(this).closest('.product-quantity').find('.quantity .qty').val(++currentQuantity);
        /*enable 'update cart' button*/
        jQuery('.button[name=update_cart]').prop("disabled", false);
        jQuery('#updateCartButtonCopy').prop("disabled", false);
    });
    jQuery('.product-quantity .minus-icon').unbind().bind("click", function() {
        var currentQuantity = jQuery(this).closest('.product-quantity').find('.quantity .qty').val();
        if (currentQuantity >= 2) {
            jQuery(this).closest('.product-quantity').find('.quantity .qty').val(--currentQuantity);
        }
        /*enable 'update cart' button*/
        jQuery('.button[name=update_cart]').prop("disabled", false);
        jQuery('#updateCartButtonCopy').prop("disabled", false);
    });

    /*Update cart button copy (this button simulates a click on the original-hidden update cart button, that is on the "Car totals" container)*/
    jQuery('#updateCartButtonCopy').on("click", function() {
        /*Simulate click on real 'update cart' button*/
        jQuery('.button[name=update_cart]').click();
    });
}