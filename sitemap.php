<?php
require('./include/config.php');
require('./include/classes.php');
require('./include/functions.php');

$xml = new SimpleXMLElement('<xml/>');
$xml->addAttribute('version', '1.0');
$xml->addAttribute('encoding','UTF-8');

$urlset = $xml->addChild('urlset');
$urlset->addAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$urlset->addAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

$pages_list = Array(
    '/',
    '/index.php',
    '/show_event.php',
    '/new_event.php',
    '/school.php',
    '/controlled_area.php',
    '/downloads.php',
    '/contact.php',
    '/faq.php',
    '/api.php',
    '/dashboard.php',
    '/information.php'
);

foreach ($pages_list as $page) {
    $url = $urlset->addChild('url');
    $url->addChild('loc','http://flightgear-atc.alwaysdata.net'.$page);
    $url->addChild('changefreq','hourly');
    $lastmod = date('Y-m-d\TH:i:s+00:00', filemtime('.'.$page));
    $url->addChild('lastmod',$lastmod);
}



header('Content-type: text/xml');
print($xml->asXML());

?>
