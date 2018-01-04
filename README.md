# Page

This is a collection of useful page functions for PHP web application.



## Usage

### Getting Started

> \$page = new Page( );

```php
// Include core Page library
require_once 'class.Page.php';

// Initialize Page object
$page = new Page();
```



### Client IP Address

Gets client IP address.

> **string** \$page->getClientIp( );

```php
// Display visitor IP address
echo 'You are connected from'.$page->getClientIp().'.';
```



### Current URL

Gets current page URL.

> **string** \$page->getCurrentUrl( );

```php
// Display current page URL
echo $page->getCurrentUrl();
```



### User Agent

Gets browser user agent.

> **string** \$page->getUserAgent( );

```php
// Display user agent
echo 'User Agent: '.$page->getUserAgent();
```



### Page Referer

Gets page referrer.

> **string** \$page->getReferer( );

```php
// Display page referer
echo $page->getReferer();
```



### HTTPS

Checks if current page is HTTPS.

> **bool** \$page->isHttps( );

```php
// Redirect to HTTPS if current page is not HTTPS
if (false == $page->isHttps()) {
	header('Location: https://www.example.com');
  	die;
}
```



### Rewrite URL

Rewrites URL with new query string.

> **string** \$page->rewrite( **string** \$url\[, **array** \$queries\] );

```php
$url = 'http://www.example.com/home.php?page=2&action=show&sort=date';

// Rewrite URL with new query string
echo $page->rewrite($url, [
	'page'		=> 3,
	'sort'		=> 'name',
]);
```

**Result:**

```
http://www.example.com/home.php?page=3&action=show&sort=name
```



###IsPost

Check whether the page request is a POST request.

> **bool** \$page->isPost();

```php
if ($page->isPost()) {
	echo 'A form is posted.';
}
```



### Request

Gets a value in HTTP `GET` or `POST` request.

> **string** \$page->request( **string** \$key\[, **int** \$method = Page::BOTH\]\[, **bool** \$html_encode = true\] );

**Method**

`Page::BOTH` Returns values in GET or POST that matches the key provided.

`Page::GET` Returns values in GET that matches the key.

`Page::POST` Returns values in POST that matches the key.

**HTML Encoded**

Enables or disables HTML encodes during display.

```php
// URL - http://www.example.com/home.php?page=1&sort=name&text=<strong>Example</strong>

echo 'Sort: '.$page->request('sort').'<br />';
echo 'Text (HTML): '.$page->request('text').'<br />';
echo 'Text: '.$page->request('text', Page::GET, false);
```

**Result:**

> Sort: name
> Text (HTML): <strong>Example</strong>
> Text: **Example**



### Get Server Variables

Gets a single or an array of server variable.

> **string** \$page->getVariable( **string|array** \$key );

```php
// Get browser language
echo "Browser Language: " . $page->getVariable('HTTP_ACCEPT_LANGUAGE');

// Get multiple server variables
$values = $page->getVariables(['SERVER_PROTOCOL', 'REQUEST_METHOD', 'QUERY_STRING']);

echo "Protocol : " . $values['SERVER_PROTOCOL'] . "\n";
echo "Method   : " . $values['REQUEST_METHOD'] . "\n";
echo "Query    : " . $values['QUERY_STRING'] . "\n";
```

