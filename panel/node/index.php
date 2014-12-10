<?php
/*
	PufferPanel - A Minecraft Server Management Panel
	Copyright (c) 2013 Dane Everitt

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see http://www.gnu.org/licenses/.
 */
namespace PufferPanel\Core;
use \Unirest;

require_once '../../src/core/core.php';

if($core->auth->isLoggedIn($_SERVER['REMOTE_ADDR'], $core->auth->getCookie('pp_auth_token'), $core->auth->getCookie('pp_server_hash')) === false) {
	Components\Page::redirect($core->settings->get('master_url').'index.php?login');
}

if($core->gsd->online() === true) {

	$response = \Unirest::get(
		"http://".$core->server->nodeData('ip').":".$core->server->nodeData('gsd_listen')."/gameservers/".$core->server->getData('gsd_id')."/getlog",
		array(
			"X-Access-Token" => $core->server->getData('gsd_secret')
		)
	);
	$content = array('contents' => $response->body->contents);

} else {
	$content = array('contents' => 'Server is currently offline.');
}

/*
 * Display Page
 */
echo $twig->render(
	'node/index.html', array(
		'server' => array_merge($core->server->getData(), array('node' => $core->server->nodeData('node'), 'console_inner' => $content['contents'])),
		'node' => $core->server->nodeData(),
		'footer' => array(
			'seconds' => number_format((microtime(true) - $pageStartTime), 4)
		)
));
?>
