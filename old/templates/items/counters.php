<?php defined('isENGINE') or die;
global $seo;
?>

<?php if (!empty($seo -> webmaster['yandex-counter'])) : ?>
<!-- Yandex.Metrika counter -->
<script<?= in('options', 'oldbrowsers') ? 'type="text/javascript"' : null; ?>>
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(<?= $seo -> webmaster['yandex-counter']; ?>, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/<?= $seo -> webmaster['yandex-counter']; ?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<?php endif; ?>

<?php if (!empty($seo -> webmaster['google-analytics'])) : ?>
<!-- Google Analytics -->
<script>
window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
ga('create', '<?= $seo -> webmaster['google-analytics']; ?>', 'auto');
ga('send', 'pageview');
</script>
<script async src='https://www.google-analytics.com/analytics.js'></script>
<!-- End Google Analytics -->
<?php endif; ?>