<?php

//////////////////
// INFORMATION //
//////////////////

// Server stats 1.0
// Get important data from server

// Created by Bondarenko Volodymyr
// Web-site: www.TrackMy.Services
// Mail: volodymyr.bondarenko.ua@gmail.com

// If you have good idea for improvement, please, don't hesitate to contact me:)

//////////////////////
// CODE START HERE //
//////////////////////


// add your services to track here
$servicesToCheck=array();
$servicesToCheck[]=array('what'=>'nginx', 'user'=>'www-data');
$servicesToCheck[]=array('what'=>'apache2', 'user'=>'www-data');
$servicesToCheck[]=array('what'=>'mysql', 'user'=>'mysql');
$servicesToCheck[]=array('what'=>'fail2ban', 'user'=>'root');
$servicesToCheck[]=array('what'=>'memcache', 'user'=>'memcache');
$servicesToCheck[]=array('what'=>'proftpd', 'user'=>'proftpd');

// set up password
$password=false;
//$password='p@$$w0rd'; //data is not available without password


// must stay without changes
class Stats{

    private $_os = 'Linux';
    private $_hostname = 'Linux';
    private $_diskInfo = array();
    private $_PIDs = array();
    private $_loadAvg = array();
	private $password = false;

    public function __construct($password){
		$this->password = $password;
        if(strpos(trim(shell_exec('uname -a')), 'FreeBSD') !== false){
            $this->_os = 'FreeBSD';
        }
        $this->_hostname = trim(shell_exec('hostname'));        
    }

    private function sendHeaders(){
        header("Content-Type: text/xml");
        header("Cache-Control: no-cache, must-revalidate");
    }
	
	private function checkPassword(){
		if(!$this->password){
			return true;
		}else{
			$password=isset($_GET['password'])?$_GET['password']:'';
			if(base64_decode($password)==$this->password){
				return true;
			}else{
				header('HTTP/1.0 401 Unauthorized');
				return false;
			}
		}
	}

    public function get($checkPIDs){
		if($this->checkPassword()){
			$this->sendHeaders();		
			foreach ($checkPIDs as $service) {
				$this->_PIDs[] = array('title' => $service['what'], 'status' => $this->ps($service['user'], $service['what']));
			}
			$this->diskSpace();
			$this->loadStats();
			$this->generateXML();
		}
    }

    private function ps($user, $what){
        $result = false;
        if($this->_os == 'FreeBSD' && $what=='apache2'){
            $what = 'httpd';
        }
        $data = explode("\n", trim(shell_exec('ps aux | grep '.$what.' | grep -v grep')));
        foreach($data as $row){
            $inString=explode(' ', $row);
            if($inString[0] == $user){
                $result = true;
                break;
            }
        }  
        return $result;
    }

    private function loadStats(){
        $this->_loadAvg = sys_getloadavg();
        if($this->_os == 'FreeBSD'){
            for($i=0; $i<3; $i++){
                $this->_loadAvg[$i] = round($this->_loadAvg[$i], 2);
            }
        }
    }

    private function diskSpace(){
        $place = explode("\n", shell_exec('df '));
        unset($place[0]);
        foreach ($place as $oneDisk) {
            $oneDisk = preg_replace('/[\s]{2,}/', ' ', $oneDisk);
            $oneDisk = explode(' ', $oneDisk);
            if(substr($oneDisk[0], 0, 5)=='/dev/'){
                if($this->_os == 'FreeBSD'){
                    $oneDisk[1]=round($oneDisk[1]/2);
                    $oneDisk[2]=round($oneDisk[2]/2);
                    $oneDisk[3]=round($oneDisk[3]/2);
                }
                $this->_diskInfo[]=array('title' => $oneDisk[0], 'size' => $oneDisk[1], 'used' => $oneDisk[2], 'free' => $oneDisk[3], 'usedPart' => $oneDisk[4], 'mounted' => $oneDisk[5]);
            }
        }
    }

    private function getSizeOfSpace($value){
        if($value > 500){
            $value = round($value/1024, 2);
            if($value > 500){
               $value = round($value/1024, 2);
                if($value > 500){
                     $value=round($value/1024, 2).' Tb';
                }else{
                    $value.= ' Gb';
                }
            }else{
                $value.= ' Mb';
            }
        }
        return $value;
    }

    private function generateXML(){
        echo '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
    <channel>
        <title>'.$this->_hostname.'</title>
        <item>
            <first>'.$this->_loadAvg[0].'</first>
            <second>'.$this->_loadAvg[1].'</second>
            <third>'.$this->_loadAvg[2].'</third>
            <space>';
		foreach($this->_diskInfo as $drive){
			//$this->getSizeOfSpace($drive['free'])
			echo '
                <disk>
                    <title>'.$drive['title'].'</title>
                    <free>'.$drive['free'].'</free>
                    <usedPart>'.$drive['usedPart'].'</usedPart>
                    <mounted>'.$drive['mounted'].'</mounted>
                </disk>';
		}
		echo '
            </space>';
            echo '
            <PIDs>';
		foreach($this->_PIDs as $service){
    
			echo '
                <service>
                    <title>'.$service['title'].'</title>
                    <status>'.($service['status']?'on':'off').'</status>
                </service>';
		}
		echo '
            </PIDs>';
        echo '
        </item>
    </channel>
</rss>';
    }
}

$data = new Stats($password);
$data->get($servicesToCheck);
