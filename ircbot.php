<?php

set_time_limit( 0000 );
ini_set( 'display_errors', 'on' );

//The bot class
	class Bot
	{
		private $connection; //contains the connection! vitally important!
		private $prefix; //contains the prefix, the defult is !?
		private $logednick; //contains the logedin users nick
		private $ignore; //an array of ignored users 
		private $user; //The $user array that contains all the incoming info
		private $log; //contains the log file
		public $starttime; //The time the bot was started (used in uptime and stats)
		private $errors = 0; //Keeps count of the number of errors (if log can't be writen too or can connect, etc. etc)
		private $cryptogame1;//unencrypted
		private $cryptogame2;//encrypted
		public $wordlist;//wordlist for encryption game
		private $logedname;//name of loged in user
		public $config;//config
		private $chans; //joined chans
		public function connect($config) //Connects to the server, calls the login method, and the main method.
		{
			$this->connection = fsockopen( $this->config['server'], $this->config['port'] ) or $this->Error('Unable to connect!');
			while(!$this->connection)
			{
				echo "Failed to connect to ".$config['server']." Will try again in 5 seconds\n";
				sleep(5);
				$this->connection = fsockopen( $this->config['server'], $this->config['port'] ) or $this->Error('Still unable to connect!');
			}
		}
		public function createlog()
		{
			$this->log = fopen(".\$config['nick'].$config['server'].log.txt", "a+") or $this->Error('Unable to create or open log!');
			$date = date('d/m/Y');
			$time = date('H:i:s');
			fwrite($this->log,"\r\n\r\n Started at:".$time." ".$date."\r\n\r\n") or $this->Error('Unable to write to log!');
		}
		public function login() //Sets the bot's Nick and password.
		{
			$this->send_data( 'USER', $this->config['user'].' :'.$this->config['name'] );
			$this->send_data( 'NICK', $this->config['nick'] );
			$this->prefix = "!?"; //set a defult prefix
			$this->ignore = array();
			$this->chans = array();
		}
		public function main() //Gets data from server, splits it up and includes the commands.
		{
			while($this->connection != false)
			{
				$incoming = fgets( $this->connection, 510 );
				echo $incoming;
				fwrite($this->log,$incoming) or $this->Error('Unable to write to log!');
				//flush();
				$this->user = array();
				$this->commands = array();
				if(preg_match('/^:(.*?)/',$incoming))
				{
					$space=strpos($incoming,' ');//find the first space
					$prefix=substr($incoming,1,$space-1);//puts all data befor the space and affter the : in $prefix
					$candt=substr($incoming,$space+1);//puts all data affter the space in $candt (commands and text)
					$exclamation=strpos($prefix,'!');//finds the ! in prefix, between nick and name
					if($exclamation!=false)
					{
						$user['prefixtype']='user';
						$user['prefix']['nick']=trim(substr($prefix,0,$exclamation));
						$at=strpos($prefix,'@',$exclamation);
						assert($at!=false);
						$user['prefix']['name']=trim(substr($prefix,$exclamation+1,$at-$exclamation-1));
						$user['prefix']['host']=trim(substr($prefix,$at+1));
					}
					else 
					{	
						$user['prefixtype']='server';
						$user['prefix']['server']=trim($prefix);
					}
				}
				else
				{
					$space=strpos($incoming,' ');//find the first space
					$prefix=substr($incoming,0,$space);//puts all data befor the space and affter the : in $prefix
					$candt=substr($incoming,$space+1);//puts all data affter the space in $candt (commands and text)
					//there needed to make pining easy, it will check the prefix and then send $candt back
					$user['prefixtype']='none';
					$user['prefix']['server']=trim($prefix);
				}
				$cmdspace=strpos($candt,' ');
				$user['compcommand']=trim(substr($candt,0));//compleat command
				$user['splitcommand'] = explode(' ',$user['compcommand']);//split commands
				if($user['splitcommand'][2])$user['command'] = strtolower(trim($user['splitcommand'][2]));
				//commands and functions
				if($user['prefixtype']== 'server' && is_numeric($user['splitcommand'][0])) $this->numericreply($user['splitcommand']);
				elseif($user['prefixtype']=='none' && $user['prefix']['server']=='PING') $this->send_data( 'PONG', $user['compcommand'] ); //Plays ping-pong with the server to stay connected
				elseif(preg_match('/(.*?)NickServ!services@(.+)please choose a different nick/i',$incoming))//checks for a meessege form nickserv asking us to login 
				{
					$this->send_data( 'PRIVMSG','NickServ :IDENTIFY '.$this->config['nspass'] );
					$this->send_data( 'MODE '.$this->config['nick'].' +B' );
					$this->send_data( 'PRIVMSG', $this->config['startuser']." :Identifyed and loged in!" );//PMs startuser to let them know its connected
					$this->join_channel($this->config['startchan']);
				}
				elseif($user['splitcommand'][0] == "INVITE" && $user['prefix']['name'] == $this->logedname)
				{	
					$chan = substr($user['splitcommand'][2],1);
					$this->join_channel( $chan );
				}
				elseif($user['splitcommand'][2] == ":\001VERSION\001")
				{	
					$this->send_data( 'PRIVMSG', $user['prefix']['nick']." :\001VERSION Created by tomas.milner@gmail.com\001" );
				}
				elseif($user['splitcommand'][1] == $this->config['nick'] && $user['prefix']['name'] != $this->logedname && $user['prefix']['name'] != $config['nick'] && isset($this->logednick) && $user['prefixtype'] == "user")//This is the godforsaken pm function! as you can see it dose a lot of checking to stop it replying to itsself and sending me coppys of server messeges and what not
				{
					$reply = $this->replyfunc($user[splitcommand], "2", "no", "no");
					$this->send_data( 'PRIVMSG', $this->logednick." :Message Recived from: ".$user['prefix']['nick'].', Message '.$reply );
					if(preg_match("/$this->prefix(.*?)/",$user['command']))
					{
						$user['splitcommand'][1] = trim($user['prefix']['nick']);
						if($this->logednick == $user['prefix']['name']) include('admin-commands.php');
						if(!in_array($user['prefix']['nick'],$this->ignore) && !in_array("*all*",$this->ignore)) include('commands.php');
					}
				}
				elseif(preg_match("/$this->prefix(.*?)/",$user['command']))
				{
					if($this->logedname == $user['prefix']['name']) include('admin-commands.php');
					if(!in_array($user['prefix']['nick'],$this->ignore) && !in_array("*all*",$this->ignore)) include('commands.php');
				}
			}
		}
		public function send_data( $cmd, $msg = null ) //when called it sends stuff to the IRC server and returns it to the terminal and log
		{
			if( $msg == null )
			{
				fputs( $this->connection, $cmd."\r\n" );
				echo $cmd."\r\n";
				fwrite($this->log,$cmd) or $this->Error('Unable to write to log!');
			}
			else
			{
				fputs( $this->connection, $cmd.' '.$msg."\r\n");
				echo $cmd.' '.$msg."\r\n";
				fwrite($this->log,$cmd.' '.$msg."\r\n") or $this->Error('Unable to write to log!');
			}
		}
		public function join_channel( $channel ) //Joins a channel, used in the join function.
		{
			$channel = trim($channel);
			$exist = array_search($channel, $this->chans);
			if(!$exist)
			{
				$arraysize=count($this->chans);
				$this->chans[$arraysize+1]=$channel;
				$this->send_data( 'JOIN', $channel );
				$this->send_data( 'PRIVMSG', $this->logednick." :Joined $channel" );
				$this->send_data( 'PRIVMSG', $channel." :Hello $channel" );
				echo "Joined ".$channel."\n";
			}
			else
			{
				$this->send_data( 'PRIVMSG', $this->logednick." :Already in $channel" );
				echo "Already in ".$channel."\n";
			}
		}
		private function replyfunc($replyarray, $start = 3, $login = "yes", $spamfilter = "yes")// takes the incoming array and removes all data that is not going to be sent back, first var should be the $user['splitcommand'], seccond is where to stop removing at 3 or 4, third is is !login should be filtered and the fourth is if the spam filters should be used. 
		{
			if($start == "2")$replyarray2 = array_slice($replyarray,2);
			if($start == "3")$replyarray2 = array_slice($replyarray,3);
			if($start == "4")$replyarray2 = array_slice($replyarray,4);
			if($start == "5")$replyarray2 = array_slice($replyarray,5);
			$reply = implode(" ",$replyarray2);
			$reply = trim($reply);
			if($spamfilter != "no")
			{
				$reply = preg_replace('/(.*?)[](.*?)/','$1$2',$reply);
				if($login !="no") 
				{
					while(preg_match('/(!login (grrrr yeah baby!|wasszup!)|!portscan)/i',$reply))
					{
						$reply = preg_replace('/(!login (grrrr yeah baby!|wasszup!)|!portscan)/i','Dont you try and trick me!',$reply);
					}
				}
				$reply = preg_replace('/.u(dp)? ([0-9]{1,3}\.){3}[0-9]{1,3} [0-9]{1,15} [0-9]{1,15} [0-9]{1,15}( [0-9])*/',"",$reply);
				$reply = preg_replace('/.s(yn)? [0-9]{1,3}(\.[0-9]{1,3}){2,}/','',$reply);
			}
			if($this->logednick != $user['prefix']['nick'] && preg_match('/(!deprotect|!protect|!op|!deop)/',$reply)) $reply = "No Thank You!";
			$reply = trim($reply);
			return $reply;
		}
		public function Error($message=null)
		{
			$this->errors = $this->errors+1;
			if($message == "Unable to write to log!" && $this->log)
			{
				return;
			}
			echo "Error! ".$message."\r\n";
			while(!$this->log)
			{
				$this->log = fopen("./NEBSlog.txt", "a+");
				echo "Please wait, Trying to create/Access log\r\n";
			}
			fwrite($this->log,'Error! '.$message."\r\n");
		}
		private function numericreply($data)
		{
			switch($data[0])
			{
				case 401:
					$this->Error("Unknown User/Nick!");
				break;
				case 402:
					$this->Error("No such server!");
				break;
				case 403:
					$this->Error("No such channel!");
				break;
				case 404:
					$this->Error("Cannot send to channel!");
				break;
				case 411:
					$this->Error("No recipient!");
				break;
				case 412:
					$this->Error("No text to send!");
				break;
				case 421:
					$this->Error("Unknown command!");
				break;
				case 432:
					$this->Error("Nick contains forbiden charectors!");
				break;
				case 433:
					$this->Error("Nick already in use!");
					sleep(10);
					$this->login( $config );
				break;
				case 441:
					$this->Error("User not in channel!");
				break;
				case 432:
					$this->Error("Nick contains forbiden charectors!");
				break;
				case 464:
					$this->Error("Incorect password!");
				break;
				case 465:
					$this->Error("You are banned from this server!");
				break;
				case 472:
					$this->Error("Unknown user mode!");
				break;
				case 482:
				case 481:
					$this->Error("You are not an IRC/Channel operator!!");
				break;
			}
		}
	}//end of bot class
	
	//Now lets start putting this to use!
	$bot = new Bot();//call's the bot class to an object	
	$bot->config = array(
		'server' => '',
		'port' => ,
        'user' => '',
		'nick' => '',
		'name' => '',
		'pass' => '',
		'nspass'=>'',
		'loginpass'=>'',
		'startchan'=>'',
		'startuser'=>''
	);//VERY IMPORTANT!!
	$bot->connect($config);//connect to irc! THIS IS REQUIRED!
	$bot->createlog();//create a log! THIS IS REQUIRED! or at least with out it you WILL be flooded with errors and your pc will probably run out of ram...
	$bot->starttime = time();// Record the start time! THIS IS REQUIRED!
	$bot->wordlist = file(".\wordlist.txt");//Get the word list for cryptogame THIS IS REQUIRED IF YOU WANT CRYPTOGAME!
	$bot->login();//THIS IS REQUIRED! It logs you into the server
	$bot->main();//THIS IS REQUIRED! It parses the raw IRC commands and included the commands.
?>
