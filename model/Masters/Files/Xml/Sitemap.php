<?php

namespace is\Masters\Files\Xml;

use is\Helpers\System;
use is\Helpers\Strings;
use is\Helpers\Objects;
use is\Helpers\Parser;
use is\Helpers\Prepare;
use is\Helpers\Paths;
use is\Helpers\Sessions;
use is\Helpers\Local;
use is\Parents\Data;
use is\Components\Router;
use is\Components\State;
use is\Components\Uri;
use is\Components\Config;
use is\Masters\View;
use is\Masters\Files\Master;

class Sitemap extends Master
{
    // https://habr.com/ru/post/358060/

    public function launch()
    {
        $view = View::getInstance();
        $config = Config::getInstance();
        $router = Router::getInstance();

        $counter = 0;

        $this->addData('domain', new Data());
        $this->addData('main', new Data());
        $this->addData('inner', new Data());
        $this->addData('content', new Data());

        // читаем пользовательские настройки карты

        $settings = Objects::create(
            [
                'lastmod' => DATE_ATOM,
                'changefreq' => [
                    'domain' => 'monthly',
                    'main' => 'daily',
                    'inner' => 'weekly',
                    'content' => 'monthly'
                ],
                'priority' => [
                    'domain' => '1.0',
                    'main' => '1.0',
                    'inner' => '0.6',
                    'content' => '0.8'
                ],
                'counter' => 50000
            ],
            $view->get('state|settings:sitemap')
        );

        // формируем базовые ссылки

        $lang = $view->get('state|lang');
        $lang_config = $config->get('default:lang');
        $domain = $view->get('state|domain') . ($lang_config && $lang !== $lang_config ? $lang . '/' : null);
        unset($lang, $lang_config);

        //$domain = $view->get('state|domain');

        // сейчас языковые файлы строятся при запросе /lang/sitemap.xml
        // но нужно было бы добавить:
        //<loc>http://www.example.com/page.html</loc>
        //<xhtml:link rel="alternate" hreflang="lang" href="http://www.example.com/lang/page.html"/>
        //<xhtml:link rel="alternate" hreflang="x-default" href="http://www.example.com/page.html"/>
        //</url>

        // еще можно строить мультиплексирование через sitemapindex:
        /*
        <sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <sitemap>
            <loc><?= $domain . $item . 'sitemap.xml'; ?></loc>
            <lastmod><?= DATE_ATOM; ?></lastmod>
        </sitemap>
        </sitemapindex>
        */

        // формируем ссылки страниц из структуры

        if ($router->structure->count()) {
            foreach ($router->structure->getData() as $item) {
                $type = $item->getEntryKey('type');

                if ($type === 'custom') {
                    continue;
                }

                $link = $item->getData('link');
                if ($link && Strings::first($link) === '/') {
                    $link = Strings::unfirst($link);
                    $len = Strings::len($link) - 1;
                    $num = Strings::find($link, '/');
                    if ($num < 0) {
                        continue;
                    }
                    $this->data[$num < $len ? 'inner' : 'main']->addData($link);
                }

                if ($type === 'content') {
                }
            }
            unset($item);
        }

        $this->data['domain']->addData('');

        // здесь должен быть чтение и вывод контента

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        // теперь просто выводим карту сайта

        Sessions::setHeader(['Content-type' => 'application/xml; charset=utf-8']);

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
        <?php
        foreach ($this->getData() as $key => $item) {
            // формирование массива
            $item = Objects::unique($item->getData());

            foreach ($item as $i) {
                ?>
<url>
    <loc><?= $domain . $i; ?></loc>
                <?php
                if ($settings['lastmod']) {
                    ?>
    <lastmod><?= date($settings['lastmod']); ?></lastmod>
                    <?php
                }
                if ($settings['changefreq'][$key]) {
                    ?>
    <changefreq><?= $settings['changefreq'][$key]; ?></changefreq>
                    <?php
                }
                ?>
    <priority><?= $settings['priority'][$key]; ?></priority>
</url>
                <?php
                $counter++;
                if ((int) $counter >= (int) $settings['counter']) {
                    break;
                }
            }
            unset($i);
        }
        unset($key, $item);
        ?>
</urlset>
        <?php
    }
}
?>