/*if device is mobile*/
if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    jQuery('body').addClass('mobile-device');
}

/*custom subtitles for x-accordion*/
/*In some cases, we want to add subtitles to the x-accordion titles. There is no such functionality in x-accordion. We will do it by taking the 'id'
and inserting it as a subtitle*/
jQuery('.x-accordion-group').each(function () {
    var accordionId = jQuery(this).attr('id');
    if (accordionId !== undefined) {
        jQuery(this).find('.x-accordion-toggle').append('<span class="subtitle" style="font-weight:normal; display:block;">' + accordionId + '</span>');
    }
});


jQuery(document).ready(function ($) {

    if (jQuery('body').hasClass('woocommerce-account')) {

        /*We want the section title for each of the woocommerce-account sections (dashboard, orders, subscriptions, etc) to appear after the navigation, and not before
        as is is by default..*/
        var title = jQuery('header.entry-header').html();
        jQuery('.woocommerce-MyAccount-navigation').append(title);
        // IF USER IS IN SUBSCRIPTIONS PAGE
        if (jQuery('.other-subscriptions').length) {

            /*ask if user has arrived from a new product activation, in order to animate the new product/order*/
            var url = window.location.href;
            if (url.indexOf('?newsubscription') !== -1) {
                setTimeout(animateNewSubscription, 1000);
            }
        }
        function animateNewSubscription() {
            jQuery('html, body').animate({
                scrollTop: jQuery('.other-subscriptions').offset().top - 200
            }, 500, function () {
                jQuery('.orders-container .order-container:first-child').addClass('new-order');
            });
        }
    }

    // if is home
    if (jQuery('body').hasClass('home')) {
        // add link to products
        jQuery('.x-feature-box.product-card').each(function () {

            /*link que cubre toda la tarjera*/
            var url = jQuery(this).find('.x-feature-box-text a').attr('href');
            jQuery(this).prepend('<a href="' + url + '" class="cover-link"></div>');

            /*animacion de entrada prematura*/
            jQuery(this).parent('.x-column').css('opacity', '1').css('transform', 'translate(0px, 0px)');

        });
    }

    /*NOTICE para dev unicamente :*/
    function alertDev() {
        var hostname = window.location.hostname;
        if (hostname === 'dev-youveda-test.pantheonsite.io') {
            alert('Atenci�n: Para agregar contenido definitivo, hacerlo en http://youveda2018.wpengine.com/ - "dev-youveda-test.pantheonsite.io" pas� a ser un entorno de prueba.');
        }
    }
    alertDev();

    // TOOLTIP FOR PRODUCT SINGLE SUBCRIPTION 
    //jQuery('.woocommerce-grouped-product-list .product-type-variable-subscription .woocommerce-grouped-product-list-item__price').append('<div class="subs-extra-descrition"><span class="youveda-tooltip"><span class="title">See details</span><span class="description">Save when you sign up for our Subscribe & Save option. Order\'s will be shipped automatically to you every month! Our doctor\'s recommend following this program for a minimum of 3 months to receive maximum benefits.</span></span></div>');
    // smooth LINK TO "GOT QUESTIONS?" SECTION 
    //jQuery('.woocommerce-grouped-product-list .product-type-variable-subscription .woocommerce-grouped-product-list-item__price').append('<div class="subs-extra-descrition"><a href="#got-questions-anchor" style="text-decoration:underline">Got Questions?</a></div>');

    // ADD SPINNER TO CHECKOUT PAGES
    if (jQuery('form.woocommerce-checkout').length) {
        // console.log('ajax');
        jQuery('form.woocommerce-checkout .billing-details-credit-card').append('<span class="youveda-ajax-loader text-center"><img src="/wp-content/themes/x-child/images/youveda-loader.svg" alt="youveda ajax loader"></span>');
    }



    /*
    Smaller Menu Size
    Cuando el usuario hace scroll down, queremos achicar el menu. Para hacerlo, quitamos el isotipo (la cara de la mujer de Youveda) y reducimos el padding vertical. Todo esto
    lo logramos agregando/quitando la clase "scrollingDown" al "header.masthead". 
    */

    /*Add the face to the logo*/
    jQuery('header.masthead .x-brand.img').prepend('<img class="isotype" src="/wp-content/uploads/2018/06/youveda-isotype.svg" alt="Youveda isotype"/>');

    var iScrollPos = 0;

    jQuery(window).scroll(function () {
        var iCurScrollPos = jQuery(this).scrollTop();
        if (iCurScrollPos > iScrollPos) {
            //Scrolling Down
            jQuery('header.masthead').addClass('scrollingDown');
        } else {
            //Scrolling Up
            var StickyNav = jQuery('.shop-nav-menu');
            if (StickyNav.index() > 0) {
                if (!StickyNav.hasClass('position-fixed')) {
                    jQuery('header.masthead').removeClass('scrollingDown');
                }
            } else {
                jQuery('header.masthead').removeClass('scrollingDown');
            }
        }
        iScrollPos = iCurScrollPos;
    });

    var lz_youtube = document.querySelectorAll('.lazy_load_youtube_video');
    if (lz_youtube.length) {
        lz_youtube.forEach(function (element) {
            if (element.dataset.embed) {
                element.addEventListener('click', function () {
                    var iframe = document.createElement('iframe');
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allowfullscreen', '');
                    iframe.setAttribute('src', 'https://www.youtube.com/embed/' + this.dataset.embed + '?rel=0&showinfo=0&autoplay=1');

                    this.innerHTML = '';
                    this.appendChild(iframe);
                });
            }
        });
    }

    // Product add to cart custom functionality
    // var qty_selector = document.querySelector('.woocommerce-grouped-product-list-item__quantity .quantity_select select');
    var variation_id_selector = document.getElementById('pa_subscription-period');
    if (variation_id_selector) {
        variation_id_selector.addEventListener('change', selected_variation_to_hidden_input);
    }
    //variation_id_selector.onchange()
    function selected_variation_to_hidden_input(e) {
        var variationHiddenInput = document.querySelector('input[type="hidden"].variation_id'),
            variation_selector = e.target;
        variationHiddenInput.value = variation_selector[variation_selector.selectedIndex].getAttribute('data-variation-id');
        if( variation_selector[variation_selector.selectedIndex].getAttribute('data-variation-id') != null ) {
            jQuery("#yv-add-to-cart").attr('href', jQuery("#yv-add-to-cart").data('url') + "?add-to-cart=" + variation_selector[variation_selector.selectedIndex].getAttribute('data-variation-id') + "&quantity=" + jQuery('#product-cart-form-group_2 .qty').val());
            jQuery('#yv-add-to-cart').removeAttr('disabled');
        } else {
            jQuery('#yv-add-to-cart').attr('disabled', 'disabled');
        }
    }

    // OnChange handler for the subscription radio button on 
    // shop main page products listing
    var product_radio_selector = document.querySelectorAll('input[name*="add_to_cart_radio_product_"]');
    if (product_radio_selector.length) {
        product_radio_selector.forEach(function (radio) {
            radio.addEventListener('change', update_add_to_cart_button_params);
        });
    }

    /**
     * Update add to cart button params on shop page after subscription selection change
     * @param  object e JS event
     */
    function update_add_to_cart_button_params(e) {
        var selectedProdId = e.target.value;
        var selectedProdSKU = e.target.getAttribute('data-product_sku');
        var addToCartBtn = e.target.parentElement.parentElement.parentElement.querySelector('.add_to_cart_button');
        addToCartBtn.setAttribute('data-product_id', selectedProdId);
        addToCartBtn.setAttribute('data-product_sku', selectedProdSKU);
        jQuery(addToCartBtn).data({
            'product_id': selectedProdId,
            'product_sku': selectedProdSKU
        });
    }

    var viewportWidth = function () { return window.innerWidth; };
    // var viewportHeight = function(){return window.innerHeight; };


    initializeOwlCarousel();

    /**
     * Initialize owlCarousel on page load
     * uses responsive breakpoints
     */
    function initializeOwlCarousel() {
        if (!checkOwlCarouselIsloaded()) {
            return;
        }
        if (viewportWidth() > 979) {
            return false;
        }
        var owl = jQuery('.owl-carousel');
        if (jQuery(owl).hasClass('owl-loaded')) {
            return false;
        }
        owl.owlCarousel({
            items: 1,
            dots: true,
            loop: true,
            nav: false,
            margin: 10,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 1
                }
            },
            checkVisibility: false,
            onResized: yvOnResizeShopPage,
            onInitialize: yv_onInitializeOwlCarousel
        });
    }

    /**
     * Handler owlCarousel display for browser resize
     * @param  object event JS event
     */
    function yvOnResizeShopPage(event) {
        var owl = event.target;
        if (viewportWidth() > 979 && jQuery(owl).hasClass('owl-loaded')) {
            jQuery(owl).trigger('destroy.owl.carousel');
            owl.querySelectorAll('li.product').forEach(function (item) {
                item.style.backgroundColor = null;
                var addToCartBtn = item.querySelector('.add_to_cart_button');
                if (addToCartBtn) {
                    var addToCartUrl = item.getAttribute('data-add-to-cart-link');
                    if (addToCartUrl) {
                        addToCartBtn.classList.add('ajax_add_to_cart');
                        addToCartBtn.setAttribute('href', addToCartUrl);
                        addToCartBtn.innerText = 'Add to Cart';
                    }
                }
            });
        }
    }

    /**
     * Triggered on owlCarousel init
     * @param  object event JS event
     */
    function yv_onInitializeOwlCarousel(event) {
        var owl = event.target;
        owl.querySelectorAll('li.product').forEach(function (item) {
            if (item.getAttribute('data-product-color')) {
                item.style.backgroundColor = item.getAttribute('data-product-color');
            }
            var addToCartBtn = item.querySelector('.add_to_cart_button');
            if (addToCartBtn) {
                var parenttUrl = item.getAttribute('data-parent-product-link');
                if (parenttUrl) {
                    addToCartBtn.classList.remove('ajax_add_to_cart');
                    jQuery(addToCartBtn).unbind('click');
                    addToCartBtn.setAttribute('data-add-to-cart-link', addToCartBtn.getAttribute('href'));
                    addToCartBtn.setAttribute('href', parenttUrl);
                    addToCartBtn.innerText = 'View Product';
                }
            }
        });
    }

    // attach even if owlCarousel has been loaded (only for the Shop page)
    if (checkOwlCarouselIsloaded()) {
        window.addEventListener('resize', resizeThrottler, false);
    }

    var resizeTimeout;

    /**
     * Handler to minimize execution for the browser resize event
     */
    function resizeThrottler() {
        // ignore resize events as long as an actualResizeHandler execution is in the queue
        if (!resizeTimeout) {
            resizeTimeout = setTimeout(function () {
                resizeTimeout = null;
                actualResizeHandler();
                // The actualResizeHandler will execute at a rate of 15fps
            }, 66);
        }
    }

    /**
     * helper function for browser resize on shop page
     * It will initialize/destroy owlCarousel
     */
    function actualResizeHandler() {
        initializeOwlCarousel();
    }

    /**
     * Checks if the owlCarousel library has been loaded on the current page
     * @return boolean 
     */
    function checkOwlCarouselIsloaded() {
        return typeof jQuery.fn.owlCarousel !== 'undefined';
    }

    /* SHOP PAGE sticky menu*/
    var stickyNav = document.querySelector('.shop-nav-menu');
    var mainNavLinks = document.querySelectorAll('.shop-nav-menu a');
    // var mainSections = document.querySelectorAll('.entry-content.content');

    activateShopStickyNav(stickyNav);


    /**
     * Activate the sticky navigation on the shop page
     */
    function activateShopStickyNav(stickyNav) {

        if (!stickyNav || typeof stickyNav === 'undefined') {
            return;
        }
        // create wrapper container
        var wrapper = document.createElement('div');

        wrapper.style.height = stickyNav.offsetHeight + 'px';
        // insert wrapper before stickyNav in the DOM tree
        stickyNav.parentNode.insertBefore(wrapper, stickyNav);

        // move stickyNav into wrapper
        wrapper.appendChild(stickyNav);
        var siteHeader = document.querySelector('.masthead');

        var stickyWaypoint;
        stickyWaypoint = new Waypoint({
            element: wrapper,
            handler: function (direction) {
                if ('down' === direction) {
                    stickyNav.classList.add('position-fixed');
                } else {
                    stickyNav.classList.remove('position-fixed');
                    siteHeader.classList.remove('scrollingDown');
                }
            },
            offset: function () {
                var substract = 0;
                if (viewportWidth() > 979) {
                    substract = 1;
                }
                return siteHeader.querySelector('.x-navbar').offsetHeight - substract + parseInt(window.getComputedStyle(document.querySelector('html')).marginTop, 10);
            }
        });

        mainNavLinks = document.querySelectorAll('.shop-nav-menu a');
        if (mainNavLinks.length) {
            mainNavLinks.forEach(function (link, index) {
                var destination = document.querySelector(link.hash);

                var inview;
                inview = new Waypoint({
                    element: destination,
                    handler: function (direction) {
                        mainNavLinks.forEach(function (item) {
                            item.parentNode.classList.remove('active');
                        });
                        if ('down' === direction) {
                            mainNavLinks[index].parentNode.classList.add('active');

                        } else if (index > 0) {
                            mainNavLinks[index - 1].parentNode.classList.add('active');
                        } else {
                            mainNavLinks[index].parentNode.classList.add('active');
                        }

                    },
                    offset: function () {
                        var offset;
                        if (siteHeader) {
                            offset = parseInt(siteHeader.querySelector('.x-navbar').offsetHeight, 10) + parseInt(stickyNav.offsetHeight, 10);
                        } else {
                            offset = stickyNav.offsetTop;
                        }
                        return viewportWidth() > 979 ? offset + 5 : offset;
                    }
                });
            });
        }

        var initScroll;
        initScroll = new SmoothScroll('.shop-nav-menu a', {
            offset: function () {
                var offset = parseInt(siteHeader.querySelector('.x-navbar').offsetHeight, 10);
                return viewportWidth() > 979 ? offset + 4 : offset;
            }
        });
    }

    // Click handler for bundle selection on shop page
    var hp_bundle_selector_wrapper = document.querySelector('.bundles-product-options .product-buttons-wrapper');
    if (hp_bundle_selector_wrapper) {
        hp_bundle_selector_wrapper.addEventListener('click', onClickBundleSelector);
    }

    // click handler for add to cart bundle
    var addToCartBtn = document.querySelector('.bundles-product-options .add_to_cart_button ');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', onClickBundleAddToCart);
    }

    /**
     * Handler for the bundle selection action on the shop page
     * @param  object event 
     */
    function onClickBundleSelector(event) {
        if (!event.target.matches('.yv-select-bundle')) {
            return;
        }
        event.preventDefault();
        var clicked = event.target,
            bundleButtons = document.querySelectorAll('.yv-select-bundle.selected'),
            countSelected = bundleButtons.length;
            
       var radioName = clicked.getAttribute('data-product-name');
       radioName = "#bundle-Section-RadioButton-" + radioName;


        if( bundleButtons.length <  2 || clicked.classList.contains('selected') ){
            var cssClassAdded = clicked.classList.toggle('selected'),
                imgSrc = clicked.getAttribute('data-bundle-product-image'),
                prodImg;
            if (cssClassAdded) {
                countSelected++;
                
                //check radio 
                jQuery(radioName).prop( "checked", true );
                //load image
                prodImg = document.querySelector('.selected-product-wrapper img:not(.visible)');
                var newThumb = document.createElement('img');
                newThumb.onload = function () {
                    prodImg.src = this.src;
                    setTimeout(function () {
                        prodImg.classList.add('visible');
                    }, 200);
                };
                newThumb.src = imgSrc;
            } else {
                countSelected--;
                //uncheck radio
                jQuery(radioName).prop( "checked", false );
                // remove image
                prodImg = document.querySelector('.selected-product-wrapper img[src="' + imgSrc + '"]');
                if (prodImg) {
                    prodImg.classList.remove('visible');
                }
            }
        }
        if (2 === countSelected) {
            bundleAddToCartEnable(true);
            var bundles_radio_selector = document.getElementById('bundle-pa_subscription-period');
            triggerEvent(bundles_radio_selector, 'change');
            showBundleMatchMsg([bundleButtons[0], bundleButtons[1] || clicked]);
        } else {
            bundleAddToCartEnable(false);
            hideBundleMatchMsg();
        }

    }

    /**
     * Enable or disable the add to cart button for the bundle section on the shop page
     * @param  boolean clickeable Activate or deactivate the click event for the button
     */
    function bundleAddToCartEnable(clickeable) {
        var enable = typeof clickeable !== 'undefined' ? clickeable : false,
            addToCartBtn = document.querySelector('.bundles-product-options .add_to_cart_button ');
        if (enable === true) {
            addToCartBtn.classList.remove('disabled');
        } else {
            addToCartBtn.classList.add('disabled');
        }
    }

    /**
     * Handler for the add to cart button on the bundles section on the shop page
     * @param  object   event 
     * @return {[type]}       [description]
     */
    function onClickBundleAddToCart(event) {
        event.preventDefault();
        var clicked = event.target;
        if (clicked.matches('.disabled')) {
            event.stopPropagation();
            return;
        }
        jQuery(clicked).removeData().data('product_id');
        jQuery(clicked).data('quantity');
        // ajax add to cart
        // Bundle buttons selected
        var bundleButtons = document.querySelectorAll('.yv-select-bundle.selected');
        // Subscription is selected ?
        var subscriptionType = document.querySelector('.radio [name="add_to_cart_radio_bundle_subscription_option"]:checked'),
            bundles_prediod_selector = document.getElementById('bundle-pa_subscription-period'),
            attrSelected;
        if (!bundleButtons || !subscriptionType) {
            return;
        }
        var postData = {};
        bundleButtons.forEach(function (button) {
            if ('yes' === subscriptionType.value) {
                attrSelected = bundles_prediod_selector[bundles_prediod_selector.selectedIndex].value;
            } else {
                attrSelected = 'no';
            }
            var bundleData = JSON.parse(button.getAttribute('data-bundle-data'));
            var bundleItemId, variationId;
            Object.keys(bundleData.bundled_items).forEach(function (BundleId) {
                if (bundleData.bundled_items[BundleId].attrs.hasOwnProperty(attrSelected)) {
                    bundleItemId = bundleData.bundled_items[BundleId].id;
                    variationId = bundleData.bundled_items[BundleId].attrs[attrSelected];
                }
            });

            if (bundleItemId && variationId) {
                postData['bundle_quantity_' + bundleItemId] = 1;
                postData['bundle_selected_optional_' + bundleItemId] = 1;
                if ('yes' === subscriptionType.value) {
                    postData['bundle_' + bundles_prediod_selector.getAttribute('name') + '_' + bundleItemId] = attrSelected;
                    postData['bundle_variation_id_' + bundleItemId] = variationId;
                }
            }

        });
        jQuery(clicked).data(postData);
        // x theme add to cart ajax activity indicator
        document.querySelector('.x-cart-notification').classList.add('bring-forward', 'appear', 'loading');
    }



    // Attach OnChange for the subscription period selector on bundles section on shop main page
    var bundles_radio_selector = document.getElementById('bundle-pa_subscription-period');
    if (bundles_radio_selector) {
        bundles_radio_selector.addEventListener('change', onBundlePeriodSelectChange);
    }

    /**
     * Handler for subscription period change on bundles section on shop main page
     * Enable or disable the add to cart button
     * @param  object   event
     */
    function onBundlePeriodSelectChange(event) {
        var variation_selector = event.target,
            selected_option = variation_selector[variation_selector.selectedIndex].value;
        bundleAddToCartEnable('' !== selected_option);
    }

    /**
     * Helper function. Source: https://plainjs.com/javascript/events/trigger-an-event-11/
     * @param  object   el   JS node
     * @param  string   type Event name
     */
    function triggerEvent(el, type) {
        // modern browsers, IE9+
        var e = document.createEvent('HTMLEvents');
        e.initEvent(type, false, true);
        el.dispatchEvent(e);
    }

    function showBundleMatchMsg(selected_items) {
        var selectedIds = [];
        selected_items.forEach(function (button) {
            var bundleData = JSON.parse(button.getAttribute('data-bundle-data'));
            selectedIds.push(bundleData.product_id);
        });
        selectedIds.sort(helper_numeric_comparision);

        var msgToShow = document.querySelector('[data-matched-ids="' + selectedIds[0] + ',' + selectedIds[1] + '"]'),
            msgContainer = document.querySelector('.choice-message');
        if (!msgToShow) {
            msgToShow = document.querySelector('[data-matched-ids="' + selectedIds[1] + ',' + selectedIds[0] + '"]');
        }
        if (msgToShow) {
            msgToShow.classList.add('visible');
            setTimeout(function () {
                msgContainer.classList.add('visible');
            }, 200);
        } else {
            hideBundleMatchMsg();
        }
    }

    function hideBundleMatchMsg() {
        var msgContainer = document.querySelector('.choice-message'),
            msgToShow = msgContainer.querySelector('.visible');
        msgContainer.classList.remove('visible');
        if (msgToShow) {
            setTimeout(function () {
                msgToShow.classList.remove('visible');
            }, 300);
        }
    }

    function helper_numeric_comparision(a, b) {
        return a - b;
    }
    /*open menu mobile on user-icon click (nav)*/
    jQuery('.x-navbar .user.mobile').click(function () {
        jQuery('#x-btn-navbar').click();
    });


    //SCROLL TO FREE SHIPPING TAB (FAQ PAGE)
    //Scroll to "Do you have free shipping tab" on the FAQ page, and open accordion
    var pageIsFAQ = window.location.href.indexOf('frequently-asked-questions');
    if (pageIsFAQ !== -1) { // if page is FAQ...
        var goToFreeShippingTab = window.location.href.indexOf('?youveda-ship');
        if (goToFreeShippingTab !== -1) { // if free-shipping query string is present..
            // go to "Do you have free shipping?" tab...
            jQuery('html, body').animate({
                scrollTop: (jQuery('#free-shipping').offset().top - 200)
            }, 1250, function () {
                // open toggle
                jQuery('#free-shipping').find('.x-accordion-toggle').click();
            });
        }
    }

    // click handler for save section on checkout
    document.addEventListener('click', onClickSaveCheckoutSection);

    // click handler for show section
    document.addEventListener('click', onClickShowCheckoutSection);


    // OnChange handler for the payment gateway on checkout.
    jQuery(document.body).on('payment_method_selected', onPaymentMethodSelected);
    // After ajax update cart event.
    jQuery(document.body).on('updated_checkout', yvOnUpdatedCheckout);
    // Custom hanlder for apply coupon.
    jQuery(document.body).on('click', '.woocommerce-checkout .apply_coupon', yvApplyCoupon);
    // Before update checkout ajax call
    jQuery(document.body).on('update_checkout', yvOnUpdateCheckout);

    /**
     * Handler for the save section button click on the checkout page
     * @param  object   event 
     * @return void
     */
    function onClickSaveCheckoutSection(event) {

        if (!event.target.matches('.save-checkout-section')) {
            return;
        }
        event.preventDefault();
        var btn = event.target;
        var sectionId = btn.getAttribute('data-yv-validate-section');
        if (!sectionId) {
            return;
        }
        var section = document.getElementById(sectionId);
        var section_billing = document.getElementById(sectionId + '_billing');
        var isValid = checkoutSectionIsValid(section);
        if (isValid) {
            var sectionInputs = section.querySelectorAll('.validate-required .input-text, .validate-required select, .validate-required input[type="checkbox"]');
            // Restore visual indicator for color validation.
            sectionInputs.forEach(function (input) {
                triggerEvent(input, 'input');
            });
            // Show saved information for Shipping/Billing
            if ('customer_details' === sectionId) {
                billingShippingSave(section, section_billing);
            }
            // collapse sections.
            navigateCheckoutSections(sectionId);
        }

        checkoutTitleValidate(section, isValid);
    }

    /**
     * Handler for the show section button click on the checkout page
     * @param  object   event 
     * @return void
     */
    function onClickShowCheckoutSection(event) {

        if (!event.target.matches('.edit-checkout-section')) {
            return;
        }

        event.preventDefault();
        var btn = event.target;
        if (btn.classList.contains('collapsed')) {
            return false;
        }
        var section = btn.closest('.checkout-section-wrapper');
        if (!section) {
            return;
        }
        // var collapseElem = section.querySelectorAll( '.collapsable' );
        // collapse sections
        // yvCollapseElem( collapseElem, 'show');
        var activeSection = document.querySelector('.active-checkout-section');
        navigateCheckoutSections(activeSection.id, section.id);
        checkoutTitleValidate(section, false);

    }
    /**
     * Validate a given checkout section
     *
     * @param  string section Section 'id'.
     * @return bool           If valid or not.
     */
    function checkoutSectionIsValid(section) {
        if (!section) {
            return false;
        }
        var sectionInputs = section.querySelectorAll('.validate-required .input-text, .validate-required select, .validate-required input[type="checkbox"]');
        sectionInputs.forEach(function (input) {
            triggerEvent(input, 'validate');
        });
        if (section.id === 'payment_details') {
            var form = document.querySelector('form.checkout');
            if (!form.querySelector('.js-sv-wc-payment-gateway-payment-token:checked')) {
                triggerEvent(form, 'checkout_place_order_' + document.querySelector('input[name="payment_method"]:checked').value);
            } else {
                // is valid if using a saved credit card
                return true;
            }
        }
        return section.querySelectorAll('.woocommerce-invalid').length === 0;
    }

    /**
     * Checkout Section title show valid on UI
     *
     * @param  obj  section        Section node.
     * @param  bool SectionIsValid Section is valid flag.
     * @return void
     */
    function checkoutTitleValidate(section, sectionIsValid) {
        var sectionTitle = section.querySelector('.checkout-section-title-collapsable h4');
        if (sectionIsValid) {
            sectionTitle.classList.add('checkout_valid_step');
        } else {
            sectionTitle.classList.remove('checkout_valid_step');
        }
    }

    /**
     * Toggler function
     *
     * @param  obj    element  Elem node | array or nodes.
     * @param  string showHide Display flag: 'show' | 'hide'.
     * @return void
     */
    function yvCollapseElem(elements, showHide) {
        if (!elements) {
            return;
        }
        var show = showHide === 'show';
        elements.forEach(function (elem) {
            if (elem.classList.contains('toggle-element')) {
                if (elem.classList.contains('default-hidden')) {
                    elem.classList.remove('default-hidden');
                } else {
                    elem.classList.toggle('collapsed');
                }
                return;
            }
            if (show) {
                elem.classList.remove('collapsed');
            } else {
                elem.classList.add('collapsed');
            }
        });
    }

    /**
     * Show billing and shipping details after saving section
     *
     * @param  obj section Seciton node.
     * @return void
     */
    function billingShippingSave(section, section_billing) {
        if (document.getElementById('amazon_customer_details')) {
            return;
        }
        var shipToDiff = section.querySelector('input[name="ship_to_different_address"]').checked,
            billingDataList = section.querySelector('ul.billing-details'),
            shippingDataList = section.querySelector('ul.shipping-details'),
            billingCountry = section_billing.querySelector('select[name="billing_country"]'),
            shippingCountry = section.querySelector('select[name="shipping_country"]'),
            shippingOption = shipToDiff ? shippingCountry : billingCountry,
            billingData = {
                'firstName': section_billing.querySelector('input[name="billing_first_name"]') ? section_billing.querySelector('input[name="billing_first_name"]').value : '',
                'lastName': section_billing.querySelector('input[name="billing_last_name"]') ? section_billing.querySelector('input[name="billing_last_name"]').value : '',
                'address': section_billing.querySelector('input[name="billing_address_1"]') ? section_billing.querySelector('input[name="billing_address_1"]').value : '',
                'address2': section_billing.querySelector('input[name="billing_address_2"]') ? section_billing.querySelector('input[name="billing_address_2"]').value : '',
                'country': billingCountry ? billingCountry.options[billingCountry.selectedIndex].text : '',
                'city': section_billing.querySelector('input[name="billing_city"]') ? section_billing.querySelector('input[name="billing_city"]').value : '',
                'state': section_billing.querySelector('[name="billing_state"]') ? section_billing.querySelector('[name="billing_state"]').value : '',
                'zip': section_billing.querySelector('input[name="billing_postcode"]') ? section_billing.querySelector('input[name="billing_postcode"]').value : '',
                'phone': section_billing.querySelector('input[name="billing_phone"]') ? section_billing.querySelector('input[name="billing_phone"]').value : ''
            },
            shippingData = shipToDiff ? {
                'address': shipToDiff ? section.querySelector('input[name="shipping_address_1"]').value : billingData.address,
                'address2': shipToDiff ? section.querySelector('input[name="shipping_address_2"]').value : billingData.address2,
                'country': shipToDiff ? shippingCountry.options[shippingCountry.selectedIndex].text : billingData.country,
                'city': shipToDiff ? section.querySelector('input[name="shipping_city"]').value : billingData.city,
                'state': shipToDiff ? section.querySelector('[name="shipping_state"]').value : billingData.state,
                'zip': shipToDiff ? section.querySelector('input[name="shipping_postcode"]').value : billingData.zip
            } : {},
            billingRows = [],
            shippingRows = [];

        // empty list
        billingDataList.querySelectorAll('li').forEach(function (elem) { elem.remove(); });
        shippingDataList.querySelectorAll('li').forEach(function (elem) { elem.remove(); });
        billingRows.push(billingData.firstName + ' ' + billingData.lastName);
        billingRows.push(billingData.address + ' ' + billingData.address2 + '. ' + billingData.city);
        billingRows.push(billingData.state + ', ' + billingData.zip);
        billingRows.push(billingData.country);
        billingRows.push(billingData.phone);
        billingRows.forEach(function (elem) {
            var li = document.createElement('li');
            li.innerText = elem;
            billingDataList.appendChild(li);
            if (!shipToDiff) {
                var clonedLi = li.cloneNode(true);
                shippingDataList.appendChild(clonedLi);
            }
        });
        if (shipToDiff) {
            shippingRows.push(billingData.firstName + ' ' + billingData.lastName);
            shippingRows.push(shippingData.address + ' ' + shippingData.address2 + '. ' + shippingData.city);
            shippingRows.push(shippingData.state + ', ' + shippingData.zip);
            shippingRows.push(shippingData.country);
            shippingRows.push(billingData.phone);
            shippingRows.forEach(function (elem) {
                var li = document.createElement('li');
                li.innerText = elem;
                shippingDataList.appendChild(li);
            });
        }

        updatedCheckoutShippingCountryFlag(shippingOption.options[shippingOption.selectedIndex].value, shippingOption.options[shippingOption.selectedIndex].text);
    }

    /**
     * OnChange event handler for payment gateway
     *
     * @param  obj e JS Event.
     * @return void
     */
    function onPaymentMethodSelected(e) {
        var option = e.target,
            parents = option.querySelectorAll('li.wc_payment_method'),
            selected = option.querySelector('input[name="payment_method"]:checked'),
            class_checked = 'checked';

        parents.forEach(function (li) {
            li.classList.remove(class_checked);
        });
        selected.closest('li.wc_payment_method').classList.add(class_checked);
        if (selected.value === 'amazon_payments_advanced') {
            document.querySelector('#pay_with_amazon img').click();
        }
    }

    function updatedCheckoutShippingCountryFlag(countryCode, country) {
        var countryFlag = document.querySelector('#delivery_details .country .flag');
        if (!countryFlag || !countryCode) {
            return;
        }
        countryFlag.classList = '';
        countryFlag.classList.add('flag', countryCode.toLowerCase());
        countryFlag.nextElementSibling.innerText = country;
    }

    /**
     * Navigate on save between checkout sections
     *
     * @param  string      sectionId The currect active section ID.
     * @param  bool|string nextItem  true | Section Id to show.
     * @return void
     */
    function navigateCheckoutSections(sectionId, nextItem = true) {
        var currentActive = document.getElementById(sectionId),
            toActivate,
            toCollapseElem,
            collapseElem = currentActive.querySelectorAll('.collapsable');

        currentActive.classList.remove('active-checkout-section');
        yvCollapseElem(collapseElem, 'hide');

        if (true === nextItem) {
            toActivate = currentActive.nextElementSibling;
        } else {
            toActivate = document.getElementById(nextItem);
        }
        if (!toActivate) {
            return;
        }
        toActivate.classList.add('active-checkout-section');
        toCollapseElem = toActivate.querySelectorAll('.collapsable');
        yvCollapseElem(toCollapseElem, 'show');
    }

    /**
     * Triggered on checkout calculate shipping ajax update
     *
     * @param  obj   e    JS Event.
     * @param  array data Returned data to update the UI.
     * @return void
     */
    function yvOnUpdatedCheckout(e, data) {
        jQuery('#delivery_details .collapsable, #payment_details .collapsable').unblock();
        var fragments = data.fragments;
        if (!fragments['.woocommerce-checkout-review-order-table']) {
            return;
        }
        var oldShipping = document.querySelector('#delivery_details ul.woocommerce-shipping-methods');
        if (oldShipping) {
            oldShipping.remove();
        }
        var oldTooltip = document.querySelector('#delivery_details .ic_tooltip');
        if (oldTooltip) {
            oldTooltip.remove();
        }

        var doc = new DOMParser().parseFromString(fragments['.woocommerce-checkout-review-order-table'], 'text/html');

        var parent = document.querySelector('#delivery_details .save-checkout-section');

        // var newShipping = doc.querySelector('ul.woocommerce-shipping-methods').cloneNode(true);

        // if ( doc.querySelector('ul.woocommerce-shipping-methods') ) {
        //     var newShipping = doc.querySelector('ul.woocommerce-shipping-methods').cloneNode(true);
        // }   

        // if ( doc ) {
        //     var newTooltip = doc.querySelector('.ic_tooltip');
        // }

        // if ( parent ) {
        //     parent.parentNode.insertBefore(newShipping, parent);
        // }

        // if (newTooltip) {
        //     parent.parentNode.insertBefore(newTooltip, parent);
        // }
    }

    /**
     * Custom handler for applying coupon
     *
     * @param  obj   e    JS Event.
     * @return void
     */
    function yvApplyCoupon(e) {
        e.preventDefault();
        var $form = jQuery('.checkout_coupon');

        if ($form.is('.processing')) {
            return false;
        }

        $form.addClass('processing').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        var data = {
            security: wc_checkout_params.apply_coupon_nonce,
            coupon_code: $form.find('input[name="coupon_code"]').val()
        };

        jQuery.ajax({
            type: 'POST',
            url: wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon'),
            data: data,
            success: function (code) {
                jQuery('.woocommerce-error, .woocommerce-message').remove();
                $form.removeClass('processing').unblock();

                if (code) {
                    var doc = new DOMParser().parseFromString(code, 'text/html');
                    $form.before(code);
                    if (!doc.querySelector('.woocommerce-error')) {
                        $form.slideUp();
                        jQuery(document.body).trigger('update_checkout', { update_shipping_method: false });
                    }
                }
            },
            dataType: 'html'
        });
    }

    /**
     * Triggered on checkout before ajax request
     *
     * @param  obj   e    JS Event.
     * @return void
     */
    function yvOnUpdateCheckout(e) {
        jQuery('#delivery_details .collapsable, #payment_details .collapsable').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }


    /*  Checkout Page Script */
    if (jQuery("body").hasClass("woocommerce-checkout")) {

        /**
         * Delivery Options : Show "For internationals customers" message tooltip message on click 
         */
        window.setInterval(function () {

            jQuery('.ic_tooltip_btn').each(function () {
                jQuery(this).unbind('click');
                jQuery(this).bind("click", function () {
                    var parent = jQuery(this).parent();
                    parent.find('.ic_tooltip_txt').toggleClass('visible');
                    if (parent.find('.ic_tooltip_txt').hasClass('visible')) { jQuery("html, body").animate({ scrollTop: jQuery(this).offset().top - 30 }); }
                });
            });
        }, 3000);

        /*==========================================================
                    Scripts Added By MakeWebBetter Start
        ===========================================================*/

        // Adapt height for new order summary.
        function reorder_coupon_section( additional_height=0 ) {

            if ( jQuery('body').hasClass('logged-as-amazon') ) {
                return;
            }

            if( additional_height == 0 ) {

                var notifcation_form = 0;
                var notifcation_login = 0;

                if( jQuery( '.woocommerce-NoticeGroup-checkout' ).length > 0 ){

                    notifcation_login = jQuery( '.woocommerce-NoticeGroup-checkout' ).height();
                }

                if( jQuery( '.woocommerce-notices-wrapper' ).length > 0 ){

                    notifcation_form = jQuery( '.woocommerce-notices-wrapper' ).height();
                }

                additional_height =  parseInt( notifcation_form ) + parseInt( notifcation_login );
            }

            // Default height.
            var upper_section_height = 33;

            // Only show point info notice.
            if ( jQuery('.wlpr_points_rewards_earn_points').length > 0 ) {
                upper_section_height = 130;
            }

            if ( jQuery( '.mwb_yv_extended_sections' ).length > 0 ) {

                var order_summ_height = jQuery( '#order_review' ).height();

                // Additional Height from Window.
                order_summ_height = parseInt( order_summ_height ) + upper_section_height;

                // Additional Height from Notification.
                order_summ_height = parseInt( order_summ_height ) + parseInt( additional_height );

                jQuery( '.mwb_yv_extended_sections' ).css( 'top', order_summ_height + 'px' );
            }
        }


        // Show and hide international shipping section.
        function manage_international_shipping_section() {
            
            var shipping_country = jQuery( '#shipping_country' ).val();
            console.log( 'updated country is ' + shipping_country );

            jQuery( '.ic_tooltip' ).hide();
            if( shipping_country !== '' && shipping_country !== 'US' ) {

                // Is an international customer.
                jQuery( '.mwb_yv_extended_sections .ic_tooltip' ).show();
            } else {

                // Is an local customer.
                jQuery( '.mwb_yv_extended_sections .ic_tooltip' ).hide();
            }
        }

        function copy_func( common_id, index, array ) {
            jQuery( '#billing_' + common_id ).prop('disabled', false);
            jQuery( '#billing_' + common_id ).val( jQuery( '#shipping_' + common_id ).val() );
        }

        // Copy shipping data to billing.
        function mwb_yv_copy_shipping_to_billing() {
            var form_fields = [
                            'first_name',
                            'last_name',
                            'country',
                            'address_1',
                            'address_2',
                            'city',
                            'state',
                            'postcode',
                            'email'
                        ];

            form_fields.forEach( copy_func );
        }
        /*==========================================================
                        Checkout Global JS 
        ===========================================================*/

        /**
         * Order button text on payment method change.
         */
        jQuery(document).on('change', '.payment_method_paypal', function() {
            jQuery( '.mwb_youveda_place_order_button' ).text( 'Proceed To Paypal' );
        });

        jQuery(document).on('change', '.payment_method_authorize_net_cim_credit_card', function() {
           jQuery( '.mwb_youveda_place_order_button' ).text( 'Place Order' );
        });

        if( jQuery( '#payment_method_paypal' ).attr( 'checked' ) ) {
            
            setTimeout( function() {
                jQuery( '.mwb_youveda_place_order_button' ).text( 'Proceed To Paypal' );
            } , 2000);
        }
        else {

            setTimeout( function() {
                jQuery( '.mwb_youveda_place_order_button' ).text( 'Place Order' );
            } , 2000);
        }

        /**
         * If logged in via amazon.
         */
         if ( jQuery('#amazon-logout').length ) {
            jQuery('body').addClass('logged-as-amazon');
         }

        /**
         * International shipping section on change shipping country.
         */
        manage_international_shipping_section();

        /**
         * International shipping section on change shipping country.
         */
        jQuery(document).on( 'change', '#shipping_country', function() {
            manage_international_shipping_section();
        });

        jQuery(document).on('click', '.mwb_youveda_place_order_button', function(e) {
            
            // Copy Shipping data to billing for No error checkout.
            // Billing-Shipping Data Error.
            if( jQuery('#bill-to-different-address-checkbox').length > 0 && jQuery('#bill-to-different-address-checkbox').prop( "checked" ) == false ) {
                e.preventDefault();
                mwb_yv_copy_shipping_to_billing();

                // After copy submit checkout form to repllicate place order button click.
                jQuery( 'form.checkout' ).submit();
            }
        });

        /*==========================================================
                        Checkout Only Mobile view JS 
        ===========================================================*/
        if( jQuery('body').hasClass('mobile-device') ) {

            var login_section = '';

            // Hide section by default.
            if ( 0 < jQuery( '.mwb_youveda_logged_in_text' ).length ) {

                login_section = jQuery( '.mwb_youveda_logged_in_text' );
                if ( login_section.hasClass( 'hide_checkout_sections' ) ) {
                    jQuery( '#customer_details' ).hide();
                }
            }

            if ( 0 < jQuery( '.woocommerce-checkout-review-order-table' ).length ) {
                jQuery( '.woocommerce-checkout-review-order-table' ).hide();
            }
            
            /** 
             * Show sections on click.
             * Login Section.
             */
            jQuery(document).on( 'click', '.mwb_youveda_logged_in_toggle', function(e) {

                login_section.toggleClass( 'hide_checkout_sections' );
                jQuery( '#customer_details' ).slideToggle( 'slow' );
            });

            /** 
             * Show sections on click.
             * Order Summary Section.
             */
            jQuery(document).on( 'click', '.order_review_heading_toggle', function(e) {

                jQuery( '.woocommerce-checkout-review-order-table' ).toggleClass( 'hidden_section' );
                jQuery( '.woocommerce-checkout-review-order-table' ).slideToggle( 'slow' );

                // Is visible.
                if ( jQuery( ".woocommerce-checkout-review-order-table" ).hasClass( 'hidden_section' ) ) { 
                  jQuery( '.order_review_arrow_img' ).toggleClass( 'order_review_arrow_up' );
                } 
                // Is Hidden.
                else {
                  jQuery( '.order_review_arrow_img' ).toggleClass( 'order_review_arrow_up' );
                }
            });
        }

        /*==========================================================
                        Checkout Only Desktop view JS 
        ===========================================================*/
        if( ! jQuery('body').hasClass('mobile-device') && jQuery( '#amazon-logout' ).length > 0 ) {
            setTimeout( function() {
                jQuery( '.woocommerce-terms-and-conditions-wrapper' ).css( 'width', '535px' );
            } , 2000);
        }

        // Desktop view Only.
        if ( ! jQuery('body').hasClass('mobile-device') ) {
            reorder_coupon_section();
        }

        // Desktop view Only.
        if ( ! jQuery('body').hasClass('mobile-device') ) {
       
            // Adapt height on checkout update.
            jQuery('body').on('updated_checkout', function () {
                console.log('checkout is being updated');
                reorder_coupon_section();
            });

            // Adapt height on change shipping.
            jQuery(document).on( 'change', '#shipping_country', function() {
                reorder_coupon_section();
            });
        }

        // Desktop view Only.
        if ( ! jQuery('body').hasClass('mobile-device') ) {
       
            // Adapt height on checkout update.
            jQuery('body').on('updated_checkout', function () {
                console.log('checkout is being updated');
                reorder_coupon_section();
            });

            // Adapt height on change shipping.
            jQuery(document).on( 'change', '#shipping_country', function() {
                reorder_coupon_section();
            });
        }

        // Desktop view Only.
        if ( ! jQuery('body').hasClass('mobile-device') ) {

            if ( jQuery('body').hasClass('logged-as-amazon') ) {

                
                // Default height.
                upper_section_height = 33;

                // Only show point info notice.
                if ( jQuery('.wlpr_points_rewards_earn_points').length > 0 ) {
                    upper_section_height = 130;
                }

                jQuery('.mwb_yv_extended_sections').css( 'right', '72px' );
                if ( jQuery( '.mwb_yv_extended_sections' ).length > 0 ) {
                    var order_summ_height = jQuery( '#order_review' ).height();
                    order_summ_height = parseInt( order_summ_height ) + upper_section_height;
                    jQuery( '.mwb_yv_extended_sections' ).css( 'top', order_summ_height + 'px' );
                }
            }
        }

        if ( ! jQuery('body').hasClass('mobile-device') ) {
           
            // If anything changes on.
            jQuery(document).on( 'click', '.mwb_youveda_place_order', function(e) {
                setTimeout( animate_coupon_section_for_notices, 3000 );
            });

            function animate_coupon_section_for_notices() {

                if ( jQuery( 'form.checkout.woocommerce-checkout' ).attr( 'novalidate' ) == 'novalidate' ) {

                    // Get Notifications Section.
                    reorder_coupon_section();
                }
            }
        }

        /*==========================================================
                    Scripts Added By MakeWebBetter End
        ===========================================================*/
    }


    /* Show/hide Add to existing subscription section */
    jQuery('#yv-show-existing-subscriptions').click(function () {
        jQuery('.yv-add-to-subscription').slideToggle();
    });


    /*==========================================================
              Checkout form Scripts Added By Ameba
    ===========================================================*/

    /* Automatically checks the 'Ship to different address?' checkbox */
    jQuery('#ship-to-different-address-checkbox').prop('checked', true);

    /* Automatically shows the billing fields if the checkbox is checked */
    if (jQuery('#bill-to-different-address-checkbox').attr('checked') == 'checked') {
        jQuery('.woocommerce-billing-fields__field-wrapper').slideDown();
    }

    /* Show/Hide billing fields */
    jQuery('#bill-to-different-address-checkbox').change(function () {
        if (jQuery('#bill-to-different-address-checkbox').attr('checked') == 'checked') {
            jQuery('.woocommerce-billing-fields__field-wrapper').slideDown();
        } else {
            jQuery('.woocommerce-billing-fields__field-wrapper').slideUp();
        }
    });

    /* Automatically fill billing fields with shipping information, */
    /* if the billing checkbox is not checked                       */
    jQuery('#shipping_first_name').change(function () {
        // if (jQuery('#billing_first_name').val() == '') {
            jQuery('#billing_first_name').val(jQuery('#shipping_first_name').val());
            jQuery('#billing_first_name').trigger('change');
        // }
    });

    jQuery('#shipping_last_name').change(function () {
        // if (jQuery('#billing_last_name').val() == '') {
            jQuery('#billing_last_name').val(jQuery('#shipping_last_name').val());
            jQuery('#billing_last_name').trigger('change');
        // }
    });


    jQuery('#shipping_city').change(function () {
        // if (jQuery('#billing_city').val() == '') {
            jQuery('#billing_city').val(jQuery('#shipping_city').val());
            jQuery('#billing_city').trigger('change');
       //  }
    });

    jQuery(document.body).on('change refresh', 'select.country_to_state, input.country_to_state ,select#shipping_state, input#shipping_state,select#shipping_country, input#shipping_country', function () {
        if (jQuery(this).attr("id") == 'shipping_state') {
            // if (jQuery('#billing_state').val() == '' || jQuery('#billing_state').val() == null) {
                jQuery('#billing_state').val(jQuery(this).val());
                jQuery('#billing_state').trigger('change');
            // }
        }
        if (jQuery(this).attr("id") == 'shipping_country') {
            // if (jQuery('#billing_country').val() == '' || jQuery('#billing_country').val() == null) {
                jQuery('#billing_country').val(jQuery(this).val());
                jQuery('#billing_country').trigger('change');
           //  }
        }
    })

    jQuery('#shipping_address_1').change(function () {
       // if (jQuery('#billing_address_1').val() == '') {
            jQuery('#billing_address_1').val(jQuery('#shipping_address_1').val());
            jQuery('#billing_address_1').trigger('change');
       // }
    });

    jQuery('#shipping_address_2').change(function () {
       // if (jQuery('#billing_address_2').val() == '') {
            jQuery('#billing_address_2').val(jQuery('#shipping_address_2').val());
            jQuery('#billing_address_2').trigger('change');
       // }
    });

    jQuery('#shipping_postcode').change(function () {
       // if (jQuery('#billing_postcode').val() == '') {
            jQuery('#billing_postcode').val(jQuery('#shipping_postcode').val());
            jQuery('#billing_postcode').trigger('change');
       // }
    });


    if (jQuery('.usremail').length > 0) {
        var userEmail = jQuery.trim(jQuery('.usremail').text());
        jQuery('.woocommerce-shipping-fields #shipping_email').val(userEmail);
    }

    if (jQuery('#shipping_first_name').val() == '') {
        if (jQuery('#billing_first_name').val() != '') {
            jQuery('#shipping_first_name').val(jQuery('#billing_first_name').val())
        }
    }

    if (jQuery('#shipping_last_name').val() == '') {
        if (jQuery('#billing_last_name').val() != '') {
            jQuery('#shipping_last_name').val(jQuery('#billing_last_name').val())
        }
    }

    jQuery('#shipping_email').change(function () {

        // MWB::Update everytime, Not just once.
        // if (jQuery('#billing_email').val() == '') {
            jQuery('#billing_email').val(jQuery('#shipping_email').val());
            jQuery('#billing_email').trigger('change');
        // }
    });

    /*

    */

    //js for radios inputs in single product
    jQuery('input[type=radio][name=add_to_cart_variation_radio]').change(function() {
        var val = jQuery(this).val();
        var p_id = jQuery(this).data('pid');
        

        if (val == "no") {
            jQuery('#add-to-cart').val(p_id);
            jQuery('#product_id').val(p_id);
            jQuery('#quantity_0').prop( "disabled", false );
            jQuery('.s0').fadeIn();
            jQuery('.s1').fadeOut();
            jQuery('#quantity_1').prop( "disabled", true );
        } else if (val == "yes") {
            jQuery('#add-to-cart').val(p_id);
            jQuery('#product_id').val(p_id);
            jQuery('.s0').fadeOut();
            jQuery('#quantity_0').prop( "disabled", true );
            jQuery('#quantity_1').prop( "disabled", false );
            jQuery('.s1').fadeIn();
        }
    });

    var default_product_id = jQuery("option[value='every-30-days']" ).data( "variation-id" );
    jQuery("#yv-add-to-cart").attr('href', jQuery("#yv-add-to-cart").data('url') + "?add-to-cart=" + default_product_id + "&quantity=1");
    jQuery("#yv-add-to-cart").removeAttr('disabled');

    jQuery('input[type=radio][name=product-selected-option]').change(function() {
        jQuery(this).parent().parent().parent().find('.product-cart-form-group').toggleClass('not-visible');
        jQuery(this).parent().parent().parent().find('.product-cart-form-group').toggleClass('visible');
        if( jQuery(this).attr('id') == 'product-selected-option_1') {
            jQuery('.variation_id').val(jQuery(this).data('id'));
            var quantity = jQuery('#product-cart-form-group_1 .qty').val();
            id = jQuery('.variation_id').val();
        } else {
            jQuery('.variation_id').val( jQuery('option[value="' + jQuery( '#pa_subscription-period').val() + '"' ).data('variation-id'));
            var quantity = jQuery('#product-cart-form-group_2 .qty').val();
            id = jQuery('.variation_id').val();
        }
        if( id != null && id != '' ) {
            jQuery("#yv-add-to-cart").attr('href', jQuery("#yv-add-to-cart").data('url') + "?add-to-cart=" + id + "&quantity=" + quantity);
            jQuery("#yv-add-to-cart").removeAttr('disabled');
        } else {
            jQuery("#yv-add-to-cart").attr('disabled', 'disabled');
        }
        jQuery(this).parent().parent().parent().find('.cart-label').toggleClass('label-selected');
        jQuery(this).parent().parent().parent().find('.cart-label').toggleClass('label-unselected');
    });

    jQuery('.qty').on('keyup input', function() {
        var group = jQuery(this).parent().parent().parent();
        if(group.attr('id') == 'product-cart-form-group_1') {
            jQuery('.variation_id').val(jQuery('#product-selected-option_1').data('id'));
            var quantity = jQuery('#product-cart-form-group_1 .qty').val();
            id = jQuery('.variation_id').val();
        } else {
            jQuery('.variation_id').val( jQuery('option[value="' + jQuery( '#pa_subscription-period').val() + '"' ).data('variation-id'));
            var quantity = jQuery('#product-cart-form-group_2 .qty').val();
            id = jQuery('.variation_id').val();
        }
        if( id != null && id != '' ) {
            jQuery("#yv-add-to-cart").attr('href', jQuery("#yv-add-to-cart").data('url') + "?add-to-cart=" + id + "&quantity=" + quantity);
            jQuery("#yv-add-to-cart").removeAttr('disabled');
        } else {
            jQuery("#yv-add-to-cart").attr('disabled', 'disabled');
        }
    });
}); /* Document ready*/


/*BLOG FILTER ICON CLICK BEHAVIOUR*/
jQuery('body.blog .filter-mobile, body.archive .filter-mobile').click(function () {
    jQuery('.categories-list').slideToggle();
});


/*FACEBOOK MODAL WINDOWN POP UP*/
jQuery(document).ready(function () {
    jQuery('.openOnModalWindow').click(function (e) {
        e.preventDefault();
        window.open(jQuery(this).attr('href'), 'fbShareWindow', 'height=450, width=550, top=' + (jQuery(window).height() / 2 - 275) + ', left=' + (jQuery(window).width() / 2 - 235) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0');
        return false;
    });
});


/* Currency converter hot fix */

jQuery(document).ready(function () {

    var conversion = 0;
    jQuery('.woocs_price_info_list li:first-child').each(function () {

        if (jQuery(this).text().indexOf('USD') > 0) { return }

        usdPrice = jQuery(this).text().substring(6); // USD: $
        parentContainer = jQuery(this).parents('.woocs_price_info').parent();

        priceContainer = jQuery('.woocommerce-Price-amount.amount', parentContainer);

        currencySymbol = jQuery('.woocommerce-Price-currencySymbol', parentContainer).text()
        price = priceContainer.text().replace(currencySymbol, '');

        console.log(price)

        if (conversion == 0) {
            conversion = parseFloat(price) / parseFloat(usdPrice)
        }

        priceContainer.text(priceContainer.text().replace(price, conversion * parseFloat(usdPrice)));
        console.log(conversion * parseFloat(usdPrice))

    })

})

/*BULLETIN SUBSCRIBE FORM - AFTER EMAIL SUBMISSION GRAVITY FORM*/

jQuery(document).ready(function () {



    jQuery(document).bind('gform_confirmation_loaded', function (event, formId) {
        if (formId === 11) {
            console.log('the bulletin');

            document.querySelector('.the-balance-bulletin').scrollIntoView({
                behavior: 'smooth'
            });

        }
    });

    if (jQuery('.gf-mailchimp-integration-checkbox').length) {
        jQuery('.gf-mailchimp-integration-checkbox').find('input[type=checkbox]').prop('checked', true);
    }
});

jQuery(document).ready(function () {
jQuery('.x-btn-navbar-woocommerce').click(function(e){
    e.preventDefault();
    jQuery('.xoo-wsc-basket').click()
})

});