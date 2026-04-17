<?php
require_once "../app/config/app.php";
require_once "../app/config/model.php";

/**
 * include all MVC PHP files
 */
function include_mvc_php_files()
{
	// include all PHP files
	foreach ( array( 'model', 'view', 'controller') as $dir )
	{
		$file_a = scandir(ROOT_DIR.$dir);

		foreach ( $file_a as $file)
		{
			if( substr( $file, -4, 4 ) != ".php" ) continue;
			require_once( ROOT_DIR.$dir.DIRECTORY_SEPARATOR.$file );
		}
	}
}

///////////////////////////////////////////////////////////////////////////////

// ROUTER
session_start();

include_mvc_php_files();

// select page to load, ie. function to call
// making router more universal => using superglobal REQUEST instead of POST or GET
$page = $_REQUEST['page'] ?? 'home';
$isApiPage = (strncmp($page, 'api_', 4) === 0);

if ($isApiPage) {
	api_handle_preflight_if_needed();
}

$main = "main_{$page}";
if (!function_exists($main)) {
	if ($isApiPage) {
		echo api_json_response(['success' => false, 'error' => 'API inconnue: ' . $page], 404);
		return;
	}

	http_response_code(404);
	echo 'Page inconnue';
	return;
}

echo $main();
