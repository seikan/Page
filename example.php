<?php

require_once 'class.Page.php';

$page = new Page();

echo '<pre>';
echo '<strong>IP Address:</strong> '.$page->getClientIp().'<br />';
echo '<strong>Current URL:</strong> '.$page->getCurrentUrl().'<br />';
echo '<strong>User Agent:</strong> '.$page->getUserAgent().'<br />';
echo '<strong>Referer:</strong> '.$page->getReferer().'<br />';
echo  '<strong>GET request:</strong>';
var_dump($page->request('page', Page::BOTH, false));
echo '<strong>Rewrite:</strong> ';

var_dump($page->rewrite($page->getCurrentUrl(), [
	'page' => 5,
	'action' => 'show',
]));
echo '</pre>';
