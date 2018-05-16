<?php
header('Content-Type: text/xml');
echo <<<SITEMAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://$_SERVER[HTTP_HOST]/</loc>
        <priority>1.00</priority>
    </url>
    <url>
        <loc>https://$_SERVER[HTTP_HOST]/user/login</loc>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://$_SERVER[HTTP_HOST]/user/register</loc>
        <priority>1.00</priority>
    </url>
    <url>
        <loc>https://$_SERVER[HTTP_HOST]/user/forgot</loc>
        <priority>1.00</priority>
    </url>
</urlset>
SITEMAP;
