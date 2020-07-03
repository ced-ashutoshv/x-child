<?php

// =============================================================================
// VIEWS/GLOBAL/_HEADER.PHP
// -----------------------------------------------------------------------------
// Declares the DOCTYPE for the site and includes the <head>.
// =============================================================================

?>

<!DOCTYPE html>

<html class="no-js" <?php language_attributes(); ?>>

<head>
	<?php if( strpos(  site_url() , 'www.youveda.com') !== false ) {?>
	<!-- Global site tag (gtag.js) - Google Analytics (back to youveda original GA account) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-84220079-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', 'UA-84220079-1');
	</script>
    <!-- End Google Tag Manager (noscript) -->
	
	<!-- OLD Blake Google tag manager? remove if seen later than July 2020 
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-PHDBMCD');</script>
	-->
	<?php  } ?>
	
	<?php wp_head(); ?>
	<script type="application/ld+json">
{
 "@context": "https://schema.org",
 "@type": "Corporation",
 "name": "Youveda",
 "alternateName": "YouVeda LLC",
 "url": "https://www.youveda.com/",
 "logo": "https://www.youveda.com/youveda-logo-02",
 "contactPoint": {
   "@type": "ContactPoint",
   "telephone": "(+1) 855-968-8332",
   "contactType": "customer service",
   "contactOption": "TollFree",
   "areaServed": "US",
   "availableLanguage": ["en","Hindi"],
   "email": "customerservice@youveda.com"
 },
 "sameAs": [
   "https://www.facebook.com/youveda.official/",
   "https://www.instagram.com/youveda_/",
   "https://www.youveda.com",
   "https://www.youtube.com/channel/UCqlTK7UgAw4-m2GOZEtUUCA",
   "https://podcasts.apple.com/us/podcast/the-balanced-being-with-gunny-sodhi"
 ]
}
</script>
<script type="application/ld+json">
{
 "@context": "https://schema.org",
 "@type": "LocalBusiness",
 "name": "Youveda",
 "image": "https://www.youveda.com/youveda-logo-02",
 "@id": "https://www.youveda.com/",
 "url": "https://www.youveda.com/",
 "telephone": "(+1) 855-968-8332",
 "priceRange": "$",
 "address": {
   "@type": "PostalAddress",
   "streetAddress": "7250 Dallas Parkway Suite 400",
   "addressLocality": "Plano",
   "addressRegion": "TX",
   "postalCode": "75024",
   "addressCountry": "US"
 },
 "geo": {
   "@type": "GeoCoordinates",
   "latitude": 33.0776152,
   "longitude": -96.8236276
 },
 "openingHoursSpecification": {
   "@type": "OpeningHoursSpecification",
   "dayOfWeek": [
     "Monday",
     "Tuesday",
     "Wednesday",
     "Thursday",
     "Friday"
   ],
   "opens": "09:00",
   "closes": "17:00"
 }
}
</script>
</head>

<body <?php body_class(); ?>>
	
<?php if( strpos(  site_url() , 'www.youveda.com') !== false ) {?>
	<!-- Google Tag Manager (noscript) Not sure we need this anymore... June 2020
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PHDBMCD"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
<?php  } ?>
	

  <div id="x-root" class="x-root">

    <?php do_action( 'x_before_site_begin' ); ?>

    <div id="top" class="site">

    <?php do_action( 'x_after_site_begin' ); ?>