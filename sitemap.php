<?php

$xml = new SimpleXMLElement('<xml/>');
$xml->addAttribute('version', '1.0');
$xml->addAttribute('encoding','UTF-8');

$urlset = $xml->addChild('urlset');
$urlset->addAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
$urlset->addAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$urlset->addAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

$url = $urlset->addChild('url');
$url->addChild('loc','http://');
$url->addChild('changefreq','hourly');

header('Content-type: text/xml');
print($xml->asXML());

?>
