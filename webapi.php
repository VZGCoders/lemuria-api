<?php
$webapi = new \React\Http\Server($loop, function (\Psr\Http\Message\ServerRequestInterface $request) use ($discord) {
	$path = explode('/', $request->getUri()->getPath());
	$sub = (isset($path[1]) ? (string) $path[1] : false);
	$id = (isset($path[2]) ? (string) $path[2] : false);
	$id2 = (isset($path[3]) ? (string) $path[3] : false);
	$ip = (isset($path[4]) ? (string) $path[4] : false);
	$idarray = array(); //get from post data
	
	if ($ip) if($GLOBALS['debug_echo']) echo '[REQUESTING IP] ' . $ip . PHP_EOL ;
	if (substr($request->getServerParams()['REMOTE_ADDR'], 0, 6) != '10.0.0')
		if($GLOBALS['debug_echo']) echo "[REMOTE_ADDR]" . $request->getServerParams()['REMOTE_ADDR'].PHP_EOL;
	$GLOBALS['querycount'] = $GLOBALS['querycount'] + 1;
	if($GLOBALS['debug_echo']) echo 'querycount:' . $GLOBALS['querycount'] . PHP_EOL;
	//logInfo('[webapi] Request', ['path' => $path]);
	switch ($sub) {
		case 'channel':
			if (!$id || !webapiSnow($id) || !$return = $discord->getChannel($id))
				return webapiFail('channel_id', $id);
			break;

		case 'guild':
			if (!$id || !webapiSnow($id) || !$return = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			break;

		case 'bans':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			$return = $guild->bans;
			break;

		case 'channels':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			$return = $guild->channels;
			break;

		case 'members':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			$return = $guild->members;
			break;

		case 'emojis':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			$return = $guild->emojis;
			break;

		case 'invites':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			$return = $guild->invites;
			break;

		case 'roles':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			$return = $guild->roles;
			break;

		case 'guildMember':
			if (!$id || !webapiSnow($id) || !$guild = $discord->guilds->offsetGet($id))
				return webapiFail('guild_id', $id);
			if (!$id2 || !webapiSnow($id2) || !$return = $guild->members->offsetGet($id2))
				return webapiFail('user_id', $id2);
			break;

		case 'user':
			if (!$id || !webapiSnow($id) || !$return = $discord->users->offsetGet($id)) {
				return webapiFail('user_id', $id);
			}
			break;

		case 'userName':
			if (!$id || !$return = $discord->users->get('name', $id))
				return webapiFail('user_name', $id);
			break;

		case 'restart':
			if (substr($request->getServerParams()['REMOTE_ADDR'], 0, 6) != '10.0.0') { //Restricted for obvious reasons
				if($GLOBALS['debug_echo']) echo '[REJECT]' . $request->getServerParams()['REMOTE_ADDR'] . PHP_EOL;
				return new \GuzzleHttp\Psr7\Response(501, ['Content-Type' => 'text/plain'], 'Reject'.PHP_EOL);
			}
			$return = 'restarting';
			//execInBackground('cmd /c "'. __DIR__  . '\run.bat"');
			//exec('/home/outsider/bin/stfc restart');
			break;

		case 'lookup':
			if (substr($request->getServerParams()['REMOTE_ADDR'], 0, 6) != '10.0.0') {
				if($GLOBALS['debug_echo']) echo '[REJECT]' . $request->getServerParams()['REMOTE_ADDR'] . PHP_EOL;
				return new \GuzzleHttp\Psr7\Response(501, ['Content-Type' => 'text/plain'], 'Reject'.PHP_EOL);
			}
			if (!$id || !webapiSnow($id) || !$return = $discord->users->offsetGet($id))
				return webapiFail('user_id', $id);
			break;

		case 'owner':
			if (substr($request->getServerParams()['REMOTE_ADDR'], 0, 6) != '10.0.0') {
				if($GLOBALS['debug_echo']) echo '[REJECT]' . $request->getServerParams()['REMOTE_ADDR'] . PHP_EOL;
				return new \GuzzleHttp\Psr7\Response(501, ['Content-Type' => 'text/plain'], 'Reject'.PHP_EOL);
			}
			if (!$id || !webapiSnow($id))
				return webapiFail('user_id', $id);
			$return = false;
			if ($user = $discord->users->offsetGet($id)) { //Search all guilds the bot is in and check if the user id exists as a guild owner
				foreach ($discord->guilds as $guild) {
					if ($id == $guild->owner_id) {
						$return = true;
						break 1;
					}
				}
			}
			break;

		case 'whitelist':
			if (substr($request->getServerParams()['REMOTE_ADDR'], 0, 6) != '10.0.0') {
				if($GLOBALS['debug_echo']) echo '[REJECT]' . $request->getServerParams()['REMOTE_ADDR'] . PHP_EOL;
				return new \GuzzleHttp\Psr7\Response(501, ['Content-Type' => 'text/plain'], 'Reject'.PHP_EOL);
			}
			if (!$id || !webapiSnow($id))
				return webapiFail('user_id', $id);
			$return = false;
			$result = array();
			if ($user = $discord->users->offsetGet($id)) { //If they're not actively in a discord server shared with the bot they probably shouldn't have access to this
				foreach ($discord->guilds as $guild) {
					$target_folder = "\\guilds\\".$guild->id;
					$whitelist_array = array();
					if(!CheckFile($target_folder, "ownerwhitelist.php")) {
						VarSave($target_folder, "ownerwhitelist.php", array());
					}else{
						$whitelist_array = VarLoad($target_folder, "ownerwhitelist.php");
					}
					if ( ($id == $guild->owner_id) || ($id == '116927250145869826') ) { //Valithor and guild owners can access
						$result[] = $guild->id;
					}elseif(!empty($whitelist_array)) { //Check array stored in guild folder to see if they've been added as whitelisted
						foreach ($whitelist_array as $target_id) { //Add the guild ID to an array if access is whitelisted
							if($target_id == $id) $result[] = $guild->id;
						}
					}
				}
				if (!empty($result)) { //Guild IDs
					$return = $result;
				}
			}
			break;

		case 'avatar':
			if (!$id || !webapiSnow($id)) {
				return webapiFail('user_id', $id);
			}
			if (!$user = $discord->users->offsetGet($id)) {
				$discord->users->fetch($id)->done(
					function ($user) {
						$return = $user->avatar;
						return new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'text/json'], json_encode($return));
					}, function ($error) {
						return webapiFail('user_id', $id);
					}
				);
				$return = 'https://cdn.discordapp.com/embed/avatars/'.rand(0,4).'.png';
			}else{
				$return = $user->avatar;
			}
			//if (!$return) return new \GuzzleHttp\Psr7\Response(($id ? 404 : 400), ['Content-Type' => 'text/plain'], ('').PHP_EOL);
			break;

		case 'avatars':
			$idarray = $data ?? array(); // $data contains POST data
			$results = [];
			$promise = $discord->users->fetch($idarray[0])->then(function ($user) use (&$results) {
			  $results[$user->id] = $user->avatar;
			});
			
			for ($i = 1; $i < count($idarray); $i++) {
			  $promise->then(function () use (&$results, $idarray, $i, $discord) {
				return $discord->users->fetch($idarray[$i])->then(function ($user) use (&$results) {
				  $results[$user->id] = $user->avatar;
				});
			  });
			}

			$promise->done(function () use ($results) {
			  return new \GuzzleHttp\Psr7\Response (200, ['Content-Type' => 'application/json'], json_encode($results));
			}, function () use ($results) {
			  // return with error ?
			  return new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], json_encode($results));
			});
			break;
		default:
			return new \GuzzleHttp\Psr7\Response(501, ['Content-Type' => 'text/plain'], 'Not implemented'.PHP_EOL);
	}
	return new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'text/json'], json_encode($return));
});
$socket = new \React\Socket\Server(sprintf('%s:%s', '0.0.0.0', '55555'), $loop);
$webapi->listen($socket);
$webapi->on('error', function ($e) {
	if($GLOBALS['debug_echo']) echo('[webapi] ' . $e->getMessage());
});