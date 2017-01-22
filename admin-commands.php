<?php
	if ($user['command']== ':'.$this->prefix.'join')
	{
		$this->join_channel( $user['splitcommand'][3] );
	}
	elseif ($user['command'] == ':'.$this->prefix.'quit')
	{
		$this->send_data( 'QUIT', "Kill_Command_Recived" );
		die;
	}
	elseif ($user['command'] == ':'.$this->prefix.'irccmd')
	{
		$reply = $this->replyfunc($user[splitcommand], "3", "no", "no");
		fputs( $this->connection, $reply."\r\n" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'part')
	{
		$partchan = $user['splitcommand'][3];
		$chanid = array_search($partchan, $this->chans);
		echo $chanid;
		if($chanid)
		{
			unset($this->chans[$chanid]);
			$this->send_data('PART', $user['splitcommand'][3]);
			$this->send_data( 'PRIVMSG', $this->logednick." :Left ".$user['splitcommand'][3] );
			echo "Left ".$user['splitcommand'][3]."\n";
		}
		else
		{
			$this->send_data( 'PRIVMSG', $this->logednick." :Left ".$user['splitcommand'][3] );
			echo "Not in ".$user['splitcommand'][3]." so can not part\n";
		}
	}
	elseif ($user['command'] == ':'.$this->prefix.'ban')
	{
		$this->send_data('WHO ', $user['splitcommand'][3]);
		$ban = fread($this->connection, 100);
		$pattern = "/352/";
		if (preg_match($pattern ,$ban))
		{
			$toban = explode(' ', $ban);
		}
		$this->send_data( 'MODE '.$user['splitcommand'][1].' +b *!*@'.$toban[5] );
		//$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Sorry This feature dosent currently work!' );
	}
	elseif ($user['command'] == ':'.$this->prefix.'unban')
	{
		$this->send_data('WHO ', $user['splitcommand'][3]);
		$ban = fread($this->connection, 100);
		$pattern = "/352/";
		if (preg_match($pattern ,$ban))
		{
			$toban = explode(' ', $ban);
		}
		$this->send_data( 'MODE '.$user['splitcommand'][1].' -b *!*@'.$toban[5] );
		//$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Sorry This feature dosent currently work!' );
	}
	elseif ($user['command'] == ':'.$this->prefix.'kb')
	{
		$this->send_data('WHO ', $user['splitcommand'][3]);
		$ban = fread($this->connection, 100);
		$pattern = "/352/";
		if (preg_match($pattern ,$ban))
		{
			$toban = explode(' ', $ban);
		}
		$this->send_data( 'MODE '.$user['splitcommand'][1].' +b *!*@'.$toban[5] );
		$this->send_data( 'KICK ', $user['splitcommand'][1].' '.$user['splitcommand'][3] );
		//$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Sorry This feature dosent currently work!' );
	}
	elseif ($user['command'] == ':'.$this->prefix.'who')
	{
		$this->send_data( 'WHO', $user['splitcommand'][3] );
		$who = fread($this->connection, 200);
		$pattern = "/352/";
		if (preg_match($pattern ,$who))
		{
			$splitwho = explode(' ', $who);
		}
		$splitwho[5] = trim($splitwho[5]);
		$user['splitcommand'][3] = trim($user['splitcommand'][3]);
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :".$user['splitcommand'][3]."'s host is $splitwho[5]" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'kick')
	{
		$this->send_data( 'KICK', $user['splitcommand'][1].' '.$user['splitcommand'][3] );
	}
	elseif ($user['command'] == ':'.$this->prefix.'do')
	{
		$reply = $this->replyfunc($user['splitcommand'], "3", "yes", "yes");
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :\001ACTION ".$reply."\001" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'test')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :This is a simple test, my Master wanted to see if i was still responding!");
	}
	elseif ($user['command'] == ':'.$this->prefix.'lagcheck')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :Lagcheck to you too" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'mode')
	{
		if (preg_match('/#(.*?)/',$user['splitcommand'][3]))
		{
			$this->send_data( 'MODE '.$user['splitcommand'][3].' '.$user['splitcommand'][4].' '.$user['splitcommand'][5] );
		}
		else
		{
			$this->send_data( 'MODE '.$user['splitcommand'][1].' '.$user['splitcommand'][4].' '.$user['splitcommand'][5] );
		}
	}
	elseif ($user['command'] == ':'.$this->prefix.'op')
	{
		$this ->send_data( 'MODE '.$user['splitcommand'][1].' +o '.$user['splitcommand'][3] );
	}
	elseif ($user['command'] == ':'.$this->prefix.'deop')
	{
		$this ->send_data( 'MODE '. $user['splitcommand'][1].' -o '.$user['splitcommand'][3] );
	}
	elseif ($user['command'] == ':'.$this->prefix.'voice')
	{
		$this ->send_data( 'MODE '. $user['splitcommand'][1].' +v '.$user['splitcommand'][3] );
	}
	elseif ($user['command'] == ':'.$this->prefix.'devoice')
	{
		$this ->send_data( 'MODE '. $user['splitcommand'][1].' -v '.$user['splitcommand'][3] );
	}
	elseif ($user['command'] == ':'.$this->prefix.'nick')
	{
		$this ->send_data( 'NICK '. $user['splitcommand'][3] );
	}
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :GOOD BYE CRULE WORLD!!!!!!!!!!!!!' );
		die('MURDERER!!');
	}
	elseif ($user['command'] == ':'.$this->prefix.'commands')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :The prefix is '.$this->prefix .' , and for you the avalible commands are: say, help, op, deop, who, quit, part, join, mode, kb, ban, unban, voice, devoice, sayhi, prefix, godieinhell, md5 --base(or md5 -b), md5, time, sha1, base64, do, irccmd, check, lagcheck, logout, ignore (-a NICK,-d NICK,-l' );
	}
	elseif ($user['command'] == ':'.$this->prefix.'prefix')
	{
		$newprefix = trim($user['splitcommand'][3]);
		$this->prefix = $newprefix;
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1].' :Prefix Changed to '.$newprefix );
	}
	elseif ($user['command'] == ':'.$this->prefix.'logout')
	{
		$logedoutuser = trim($user['splitcommand'][3]); 
		$this->logednick = "";
		$this->send_data( 'PRIVMSG', $logedoutuser.' :you have logout' );
	}
	elseif ($user['command'] == ':'.$this->prefix.'great')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :Hello ".$user['splitcommand'][3]."!!" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'topic')
	{
		$reply = $this->replyfunc($user[splitcommand], "3", "no", "yes");
		$this->send_data( 'TOPIC', $user['splitcommand'][1]." :$reply" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'notice')
	{
		$reply = $this->replyfunc($user[splitcommand], "4", "yes", "yes");
		$this->send_data( 'NOTICE', $user['splitcommand'][3]." :$reply" );
	}
	elseif ($user['command'] == ':'.$this->prefix.'ignore')
	{
		if($user['splitcommand'][3] == "-a")
		{
			$ignoreuser = $user['splitcommand'][4];
			$arraysize=count($this->ignore);
			$this->ignore[$arraysize+1]=$ignoreuser;
			$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :".$ignoreuser." added to the ignore list" );
		}
		elseif($user['splitcommand'][3] == "-r")
		{
			$ignoreuser = $user['splitcommand'][4];
			$userid = array_search($ignoreuser, $this->ignore);
			unset($this->ignore[$userid]);
			$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :".$ignoreuser." removed from the ignore list" );
		}
		elseif(trim($user['splitcommand'][3]) == "-l")
		{
			sort($this->ignore);
			trim($this->ignore);
			$ignoredusers = implode($this->ignore,", ");
			$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :The following users are being ignored :".$ignoredusers );
		}
	}
	elseif ($user['command'] == ':'.$this->prefix.'flood')
	{
		$reply = $this->replyfunc($user['splitcommand'], "5", "yes", "yes");
		for($i=0; $i<$user['splitcommand'][3]; $i++)
		{
			$this->send_data( 'PRIVMSG', $user['splitcommand'][4]." :$reply" );
		}
	}
	elseif($user['command'] == ':'.$this->prefix.'chans')
	{
		sort($this->chans);
		trim($this->chans);
		$chans = implode($this->chans,", ");
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :I am in the following chans : ".$chans );
	}
	elseif ($user['command'] == ':'.$this->prefix.'rape')
	{
		$this->send_data( 'PRIVMSG', $user['splitcommand'][1]." :\001ACTION is rapeing ".$user['splitcommand'][3]."\001" );
	}
?>
