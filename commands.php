<?php
	if ($user['command'] == ':'.$this->prefix.'sayhi')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :Hello! I am an IRC Bot!!!" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'help')
	{
		$subject = strtolower(trim($user['splitcommand'][3]));
		if(isset($user['splitcommand'][3]))
		{
			if ($subject == 'irc')
			{
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :IRC (Internet Relay Chat), Its what your currently useing, want to try some commands? Try /quit .' );
			}
			elseif ($subject == 'scientolagy')
			{
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :I refuse to help with this.' );
			}
			elseif ($subject == 'fail')
			{
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :This is what you do at life!.' );
			}
			else
			{
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Unknown help subject' );
			}
		}
		else
		{
			$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Im an IRC Bot, What do you need help with?' );
			$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :IRC' );
		}
	}
	elseif ($user['command'] == ':'.$this->prefix.'say')
	{
		$reply = $this->replyfunc($user['splitcommand'], "3", "yes", "yes");
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :$reply" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'games')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :I have the games : !?cryptogame." );
	}
	elseif ($user['command'] == ':'.$this->prefix.'commands' && $this->logednick != $user['prefix']['user'])
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :The prefix is '.$this->prefix .' , and for you the avalible commands are :say, help, sayhi, md5, md5 -b, sha1, cryptogame and commands' );
	}
	elseif ($user['command'] == ':'.$this->prefix.'login')
	{
		if($this->config['loginpass'] == $user['splitcommand'][3])
		{	
			$this->logednick = trim($user['prefix']['nick']);
			$this->logedname = trim($user['prefix']['name']);
			$this->send_data( 'PRIVMSG', $user['prefix']['nick'].' :you have loged in '.$user['prefix']['nick'] );
		}
	}
	elseif ($user['command'] == ':'.$this->prefix.'time')
	{
		$time = date('H:i:s'); 
		$date = date('d/m/Y');
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :It is '.$time.' on the '.$date  );
	}
	elseif ($user['command'] == ':'.$this->prefix.'stats')
	{
		$diftime = time() - $this->starttime;
		$seconds = $diftime % 60;
		$minutes = floor(($diftime % 3600)/60);
		$hours = floor(($diftime % 216000)/3600);
		$moddate =  date ("d/m/Y.", getlastmod());
		$ram = memory_get_usage();
		$ram = round(($ram/1024)/1024,4);
		if($hours != 0)$uptime = "I have been running for : ".$hours." hours ".$minutes." minutes and ".$seconds." seconds!";
		else $uptime = "I have been running for :".$minutes." minutes and ".$seconds." seconds!";
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :I was last modifed on the : '.$moddate.' and '.$uptime.', I have had '.$this->errors.' errors in that time. I am useing '.$ram.'MB of RAM'  );
	}
	elseif ($user['command'] == ':'.$this->prefix.'uptime')
	{
		$diftime = time() - $this->starttime;
		$seconds = $diftime % 60;
		$minutes = floor(($diftime % 3600)/60);
		$hours = floor(($diftime % 216000)/3600);
		if($hours != 0)$uptime = "I have been running for : ".$hours." hours ".$minutes." minutes and ".$seconds." seconds!";
		else $uptime = "I have been running for :".$minutes." minutes and ".$seconds." seconds!";
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :'.$uptime  );
	}
	elseif ($user['command'] == ':'.$this->prefix.'md5')
	{	
		if($user['splitcommand'][3] == "--base64" || $user['splitcommand'][3] == "-b")
		{
			$user['splitcommand'][4] = $this->replyfunc($user[splitcommand], "4", "no", "no");
			$val = base64_encode(trim($user['splitcommand'][4]));
			$oval = $user['splitcommand'][4];
		}
		else
		{
			$user['splitcommand'][3] = $this->replyfunc($user[splitcommand], "3", "no", "no");
			$val = trim($user['splitcommand'][3]);
			$oval = $user['splitcommand'][3];
		}
		$md5val=md5($val);
		$md5val=trim($md5val);
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :The MD5'd version of ".$oval." is ".$md5val  );
	}
	elseif ($user['command'] == ':'.$this->prefix.'sha1')
	{	
		$val = trim($user['splitcommand'][3]);
		$encryptval=sha1($val);
		$encryptval=trim($encryptval);
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :The SHA1'd version of ".$val." is ".$encryptval  );
	}
	elseif ($user['command'] == ':'.$this->prefix.'base64')
	{	
		$val = trim($user['splitcommand'][3]);
		$encryptval=base64_encode($val);
		$encryptval=trim($encryptval);
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :The base64'd version of ".$val." is ".$encryptval  );
	}
	elseif ($user['command'] == ':'.$this->prefix.'cryptogame')
	{
		if($user['splitcommand'][3] == 'guess' && isset($this->cryptogame1))
		{
			if($user['splitcommand'][4] == $this->cryptogame1)
			{
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :WOW!! DUDE YOU GOT IT!!"  );
				unset($this->cryptogame1);
			}
			else $this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :Nope you got it wrong!"  );
		}
		elseif($user['splitcommand'][3] == 'start' && !isset($this->cryptogame1))
		{
			if($user['splitcommand'][4] == 'md5')
			{
				$wordlistsize = count($this->wordlist);
				$rand = rand(0,$wordlistsize);
				$this->cryptogame1 = trim($this->wordlist[$rand]);
				$this->cryptogame2 = md5($this->cryptogame1);
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Cryptogame started, please work out '.$this->cryptogame2 );
				echo $this->cryptogame1."\r\n";
			}
			elseif($user['splitcommand'][4] == 'sha1')
			{
				$wordlistsize = count($this->wordlist);
				$rand = rand(0,$wordlistsize);
				$this->cryptogame1 = trim($this->wordlist[$rand]);
				$this->cryptogame2 = sha1($this->cryptogame1);
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Cryptogame started, please work out '.$this->cryptogame2 );
				echo $this->cryptogame1."\r\n";
			}
			elseif($user['splitcommand'][4] == 'rot')
			{
				$wordlistsize = count($this->wordlist);
				$rand = rand(0,$wordlistsize);
				$randrot = rand(0,25);
				$this->cryptogame1 = trim($this->wordlist[$rand]);
				$this->cryptogame2 = $this->cryptogame1;
				for($i=0; $i<strlen($this->cryptogame2); $i++)
				{
					$char = $this->cryptogame2[$i];
					$ascii = ord($char);
					if($ascii >= 65 && $ascii < 91)
					{
						$ascii = $ascii - 65;
						$newascii = $ascii + $randrot;
						$newascii = $newascii % 26;
						$newascii = $newascii + 65;
					}
					elseif($ascii >= 97 && $ascii <= 122)
					{
						$newascii = $ascii + $randrot;
						$newascii = $newascii % 26;
						$newascii = $newascii + 97;
					}
					elseif($ascii >= 48 && $ascii <= 57)
					{
						$ascii = $ascii - 48;
						$newascii = $ascii + $randrot;
						$newascii = $newascii % 10;
						$newascii = $newascii + 48;
					}
					else
					{
						$newascii = $ascii;
					}
					$char = chr($newascii);
					$this->cryptogame2[$i] = $char;
				}
				$this->cryptogame2 = trim($this->cryptogame2);
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Cryptogame started, please work out '.$this->cryptogame2 );
				echo $this->cryptogame1."\r\n";
			}
			else 
			{
				$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Cryptogame: Type '.$this->prefix.'cryptogame start (rot|sha1|md5), to start and '.$this->prefix.'cryptogame guess, to have a guess at the answer'  );
			}
		}
		else 
		{
			$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Cryptogame: Type '.$this->prefix.'cryptogame start (rot|sha1|md5), to start and '.$this->prefix.'cryptogame guess, to have a guess at the answer'  );
		}
	}
	elseif ($user['command'] == ':'.$this->prefix.'rand')
	{
		$randnum = rand(0,count($rand));
		$rand = array(
		);
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :".$rand[$randnum] );
	}
?>
