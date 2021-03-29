<?php defined('isENGINE') or die; 

global $seo;
global $uri;

//print_r($seo);
//print_r($uri);
//print_r($lang);

/*
media="(orientation: portrait)"
media="(orientation: landscape)"
media="print" для печати и для режима "для слабовидящих"
*/

?>

<?php if (!empty($seo -> webmaster['google-counter'])) : ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $seo -> webmaster['google-counter']; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?= $seo -> webmaster['google-counter']; ?>');
</script>
<?php endif; ?>

<!-- META -->

<?php if (in('options', 'oldbrowsers')) : ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Language" content="<?= $lang -> lang; ?>" />
<?php endif; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php if (in('options', 'viewport')) : ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<?php endif; ?>

<!-- Html Metatags -->

<title><?php

	if (thispage('home') && !empty($seo -> home)) {
		echo $seo -> home;
	} else {
		echo set($seo -> prefix, true) . $seo -> title . (!empty($seo -> postfix) ? $seo -> postfix : ' - ' . lang('title'));
	}
	
?></title>

<?php
	foreach (['description', 'keywords', 'author', 'copyright', 'robots'] as $item) :
		if (!empty($seo -> $item)) :
?>
<meta name="<?= $item; ?>" content="<?= $seo -> $item; ?>" />
<?php
		endif;
	endforeach;
	unset($item);
?>

<?php if (objectIs($seo -> options) && in_array('dublincore', $seo -> options)) : ?>
<!-- Dublin Core -->

<meta name="DC.Title" content="<?= $seo -> title; ?>">
<meta name="DC.Creator" content="<?= lang('title'); ?>">
<meta name="DC.Subject" content="<?= $seo -> keywords; ?>">
<meta name="DC.Description" content="<?= $seo -> description; ?>">
<meta name="DC.Date" content="<?= $seo -> created['w3cdtf']; ?>">
<meta name="DC.Identifier" content="<?= $uri -> url; ?>">
<meta name="DC.Rights" content="<?= $seo -> copyright; ?>">
<meta name="DC.Publisher" content="<?= $seo -> author; ?>">
<meta name="DC.Type" content="Text">
<meta name="DC.Format" content="text/html">
<meta name="DC.Language" content="<?= $lang -> lang . '_' . mb_strtoupper($lang -> code); ?>">
<meta name="DC.Coverage" content="World">
<?php endif; ?>

<?php if (objectIs($seo -> options) && in_array('opengraph', $seo -> options)) : ?>
<!-- OpenGraph -->

<meta property="og:title" content="<?= $seo -> title; ?>" />
<meta property="og:image" content="<?= $seo -> image; ?>" />
<meta property="og:url" content="<?= $uri -> url; ?>" />
<meta property="og:site_name" content="<?= lang('title'); ?>" />
<meta property="og:description" content="<?= $seo -> description; ?>" />
<meta property="og:locale" content="<?= $lang -> lang . '_' . mb_strtoupper($lang -> code); ?>" />

<?php
	if (!empty($seo -> langs)) :
		foreach ($seo -> langs as $key => $item) :
?>
	<meta property="og:locale:alternate" content="<?= $key . '_' . mb_strtoupper($item); ?>" />
<?php
		endforeach;
		unset($key, $item);
	endif;
?>

<?php if (objectGet('content', 'name')) : ?>
	<meta property="og:type" content="article" />
	<meta property="article:author" content="<?= $seo -> author; ?>" />
	<meta property="article:published_time" content="<?= $seo -> created['w3cdtf']; ?>" />
	<meta property="article:modified_time" content="<?= $seo -> modified['w3cdtf']; ?>" />
	<meta property="article:section" content="<?= objectGet('content', 'parent'); ?>" />
	<?php
		if (!empty($seo -> tags)) :
			foreach ($seo -> tags as $item) :
	?>
	<meta property="article:tag" content="<?= $item; ?>">
	<?php
			endforeach;
			unset($item);
		endif;
	?>
<?php else : ?>
	<meta property="og:type" content="website" />
<?php endif; ?>

<?php endif; ?>

<?php if (objectIs($seo -> options) && in_array('twitter', $seo -> options)) : ?>
<!-- Twitter Card -->
<!-- https://dev.twitter.com/docs/cards/validation/validator -->
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="<?= $seo -> title; ?>" />
<meta name="twitter:description" content="<?= $seo -> description; ?>" />
<meta name="twitter:url" content="<?= $uri -> url; ?>" />
<meta name="twitter:image" content="<?= $seo -> image; ?>" />
<?php endif; ?>

<?php if (objectIs($seo -> options) && in_array('schema', $seo -> options)) : ?>
<!-- Google Schema -->
<meta itemprop="name" content="<?= $seo -> title; ?>" />
<meta itemprop="description" content="<?= $seo -> description; ?>" />
<meta itemprop="image" content="<?= $seo -> image; ?>" />
<?php endif; ?>

<?php if (objectIs($seo -> options) && in_array('business', $seo -> options)) : ?>
<!-- Google Business -->
<meta property="business:contact_data:street_address" content="<?= lang('information:address'); ?>" />
<meta property="business:contact_data:locality" content="<?= lang('information:city'); ?>" />
<meta property="business:contact_data:postal_code" content="<?= lang('information:postcode'); ?>" />
<meta property="business:contact_data:country_name" content="<?= lang('information:country'); ?>" />
<meta property="business:contact_data:email" content="<?= lang('information:email', 'return'); ?>" />
<meta property="business:contact_data:phone_number" content="<?= lang('information:phone', 'return'); ?>" />
<meta property="business:contact_data:website" content="<?= $uri -> site; ?>" />
<?php endif; ?>

<!-- Additional metatags -->

<?php
	if (!empty($seo -> additional)) :
		foreach ($seo -> additional as $key => $item) :
?>
<meta name="<?= $key; ?>" content="<?= $item; ?>" />
<?php
		endforeach;
	endif;
?>

<!-- Search manage links -->

<?php if (!empty($seo -> filter) || !empty($lang)) : ?>

<?php if (!empty($seo -> filter)) : ?>
<meta name="robots" content="noindex, nofollow">
<?php else : ?>
<meta name="referrer" content="origin">
<?php endif; ?>
<link rel="canonical" href="<?= $uri -> site . $uri -> path -> string . (empty($seo -> filter) ? $uri -> query -> string : null); ?>">
<?php
	if (!empty($seo -> langs)) :
		foreach ($seo -> langs as $key => $item) :
?>
	<link rel="alternate" href="<?= $uri -> site . $key . '/' . $uri -> path -> string . (empty($seo -> filter) ? $uri -> query -> string : null); ?>" hreflang="<?= $key; ?>">
<?php
		endforeach;
		unset($key, $item);
	endif;
?>

<?php elseif (DEFAULT_PAGE && empty($uri -> path -> file)) : ?>
<link rel="canonical" href="<?= $uri -> site . $uri -> path -> string . 'index.' . (DEFAULT_PAGE === true ? 'html' : DEFAULT_PAGE); ?>">
<?php endif; ?>

<base href="<?= $uri -> site; ?>" />

<?php if (!empty($seo -> webmaster['yandex-verification'])) : ?>
<meta name="yandex-verification" content="<?= $seo -> webmaster['yandex-verification']; ?>" />
<?php endif; ?>
<?php if (!empty($seo -> webmaster['google-verification'])) : ?>
<meta name="google-site-verification" content="<?= $seo -> webmaster['google-verification']; ?>" />
<?php endif; ?>
