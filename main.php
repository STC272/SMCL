<?php
/*****************************************************************************************************/
/*   ____        _                      __  __  ____   _                           _                 */
/*  / ___|  __ _| | ___   _ _ __ __ _  |  \/  |/ ___| | |    __ _ _   _ _ __   ___| |__   ___ _ __   */
/*  \___ \ / _` | |/ / | | | '__/ _` | | |\/| | |     | |   / _` | | | | '_ \ / __| '_ \ / _ \ '__|  */
/*   ___) | (_| |   <| |_| | | | (_| | | |  | | |___  | |__| (_| | |_| | | | | (__| | | |  __/ |     */
/*  |____/ \__,_|_|\_\\__,_|_|  \__,_| |_|  |_|\____| |_____\__,_|\__,_|_| |_|\___|_| |_|\___|_|     */
/*                                                                                                   */
/*                                             Sakura Minecraft Launcher ӣ�������� by KasuganoSora  */
/*                                                                                                   */
/*  ����һ����Դ������������������� GPL v3 ��ԴЭ���ǰ��������ʹ�ñ������                         */
/*                                                                                                   */
/*  ���ߣ�KasuganoSora  ������https://www.moemc.cn/  ���ͣ�https://blog.kasuganosora.cn/             */
/*                                                                                                   */
/*****************************************************************************************************/

// ����ȫ�ֱ������汾��Ϣ������Ŀ¼��ϵͳ����
define("LOCALVERSION", "1.2.0.201");
define("ROOT", str_replace("\\", "/", __DIR__));
define('IS_WIN',strstr(PHP_OS, 'WIN') ? true : false);

// ����������
class SoraOS {
	
	private $downloadProgress;
	private $downloadSize;
	public static $global_user;
	public static $global_pass;
	public static $mainClass;
	public static $minecraftArguments;
	
	// ������������
	public function onEnable() {
		
		echo file_get_contents(ROOT . "/php/motd.txt");
		sleep(3);
		echo "KasuganoSora Launcher\n";
		echo "Local version: " . LOCALVERSION . "\n";
		sleep(1);
		$this::logs("ROOT DIR SET: " . ROOT);
		$this::logs(IS_WIN ? "WINDOWS SYSTEM: TRUE" : "WINDOWS SYSTEM: FALSE");
		$this::logs("�������� KasuganoSora �������������...");
		$this::checkUpdate();
		$this::loadConfig();
		$this::home();
		
	}
	
	// ������������
	public function onDisable() {
		
		$this::logs("Stopping launcher service...");
		sleep(1);
		$this::logs("���ڽ�������������...");
		
	}
	
	// �����º���
	private function checkUpdate() {
		
		$this::logs("�������ӵ��������Լ���Ƿ��и���...");
		$newVersion = @file_get_contents("https://api.tcotp.cn:4443/Launcher/update/?s=launcher&version=1.4");
		if($newVersion == "") {
			$this::logs("��������ʧ�ܣ��������������Ƿ�������");
		} elseif($newVersion !== LOCALVERSION) {
			$this::logs("�����������°汾��" . $newVersion . " ���ذ汾��" . LOCALVERSION);
		} else {
			$this::logs("���¼����ϣ��������°汾��");
		}
		
	}
	
	// ������ҳ����
	private function home() {
		
		echo " ��������������������������������������������������������������������������\n\n";
		echo " KasuganoSora �������������\n\n";
		echo " ��ѡ�����Ĳ�����������ź�س�\n\n";
		echo " 1.������Ϸ  2.��Ϸ����  3.�˳�������\n\n";
		echo " Input> ";
		$select = trim(fgets(STDIN));
		echo "\n ����������������������������������������������������������������������������\n";
		switch($select) {
			case "1":
				$this::logs("����������Ϸ...");
				$this::launcher();
				break;
			case "2":
				$this::logs("���ڼ�������...");
				$this::logs("�����������ó���...");
				$this::setting();
				break;
			case "3":
				$this::shutdown(0);
				break;
			default:
				$this::logs("��Ч��ѡ��...");
				sleep(2);
				$this::home();
		}
		
	}
	
	// ��������ҳ����
	private function setting() {
		
		$cfg = @json_decode(file_get_contents("Launcher.json"), true);
		echo " ��������������������������������������������������������������������������\n\n";
		echo " KasuganoSora �������������\n\n";
		echo " ��ѡ����Ҫ�����İ汾������Ϊ�ҵ��İ汾�б�������� ID ���ɣ�������ʹ���ѱ��������\n\n";
		$path = ROOT . "/.minecraft/versions/";
        $prevpath = @dirname($path);
        $dir_handle = @opendir($path);
        $versionlist = Array();
        $gid = 1;
		echo "  ID\t����\n";
        while($file = @readdir($dir_handle)) {
            if($file !== "." && $file !== "..") {
                if(is_dir($path . $file)) {
                    if(file_exists($path . $file . "/" . $file . ".json")) {
                        echo "  " . $gid . "\t" . $file . "\n";
						$versionlist[$gid] = $file;
						$gid++;
                    }
                }
            }
        }
        closedir($dir_handle);
		echo "\n Version> ";
		$version = trim(fgets(STDIN));
		echo "\n";
		if(!file_exists($path . $versionlist[$version] . "/")) {
			echo " �汾�����ڻ�ѡ�����������ѡ��";
			sleep(3);
			$this::setting();
		}
		$sversion = $versionlist[$version];
		if(!isset($versionlist[$version])) {
			if(!isset($cfg['version'])) {
				echo "\n û���Ѿ�����İ汾��������ѡ��\n";
				sleep(3);
				$this::home();
			} else {
				$sversion = $cfg['version'];
			}
		}
		echo " ���������� Java ·����������ʹ���ѱ�������ã����Զ�����\n\n";
		echo " Java> ";
		$javapath = trim(fgets(STDIN));
		if(!file_exists($javapath) && $javapath !== "") {
			echo "\n Java ·�����ò���ȷ�������ļ��Ƿ���ڡ�";
			sleep(3);
			$this::setting();
		}
		if($javapath == "") {
			$javapath = "java";
		}
		echo "\n";
		echo " ����������ڴ棬��λ MB��������ʹ��Ĭ��ֵ 1024\n\n";
		echo " Ram> ";
		$maxram = trim(fgets(STDIN));
		if($maxram == "") {
			$maxram = 1024;
		}
		$maxram = intval($maxram);
		if($maxram == 0) {
			echo "\n ����ڴ�����������ұ������ 0";
			sleep(3);
			$this::setting();
		}
		echo "\n �����������˺ţ�����������������¼������ #���������޸��ѱ�����˺š�\n\n";
		echo " User> ";
		$online_user = trim(fgets(STDIN));
		if($online_user == "") {
			if(!isset($cfg['user'])) {
				echo "\n û���Ѿ�������˺ţ����������ѽ��á�\n";
				$online_user = "#";
			} else {
				$online_user = $cfg['user'];
			}
		}
		if($online_user !== "#") {
			echo "\n �������������룬�������޸��ѱ�������롣\n\n";
			echo " Pass> ";
			$online_pass = trim(fgets(STDIN));
			if($online_pass == "") {
				$online_pass = $cfg['pass'];
			}
		}
		if($online_user == "#" || $online_user == "") {
			echo "\n ��������Ϸ���֣��������޸��ѱ�������֡�\n\n";
			echo " Name> ";
			$username = trim(fgets(STDIN));
			if($username == "") {
				if(!isset($cfg['user'])) {
					echo "\n û���Ѿ��������Ϸ���֣����ñ��жϡ�\n";
					sleep(3);
					$this::home();
				} else {
					$username = $cfg['name'];
				}
			}
		}
		$arr = Array(
			'version' => $sversion,
			'java' => $javapath,
			'ram' => $maxram,
			'user' => $online_user,
			'pass' => $online_pass,
			'name' => $username
		);
		echo "\n ����������������Ϣ���Ƿ񱣴棿\n\n";
		echo "   ѡ��汾��" . $sversion . "\n\n";
		echo "   Java·����" . $javapath . "\n\n";
		echo "   ����ڴ棺" . $maxram . "\n\n";
		if($online_user !== "#" && $online_user !== "" && $online_pass !== "") {
			echo "   �����˺ţ�" . $online_user . "\n\n";
			echo "   �������룺" . str_repeat("*", mb_strlen($online_pass)) . "\n\n";
		}
		if(isset($username)) {
			echo "   ��Ϸ���֣�" . $username . "\n\n";
		}
		echo " ���� n ȡ�����棬�����������ݱ��档\n\n";
		echo " Save> ";
		$save = trim(fgets(STDIN));
		if(strtolower($save) == "n") {
			echo "\n �����ļ�δ�޸ġ�";
			sleep(3);
			$this::home();
		} else {
			@file_put_contents("Launcher.json", json_encode($arr));
			if(isset($username)) {
				$this->global_user = $username;
			}
			echo "\n �����ļ��ɹ����棡";
			sleep(3);
			$this::home();
		}
		
	}
	
	// ���������ļ�
	private function loadConfig() {
		
		$launcher = @json_decode(file_get_contents("Launcher.json"), true);
		if(!$launcher) {
			$this::setting();
		}
		if(isset($launcher['name'])) {
			$this->global_user = $launcher['name'];
		}
		
	}
	
	// ��־��¼����
	public function logs($data, $level = "INFO") {
		
		$dateformart = "[" . date("H:i:s") . " " . $level . "] ";
		echo $dateformart . $data . "\n";
		
	}
	
	// ��Ϸ��������
	public function launcher() {
		
		// ������������ò��������������ҳ
		if(!file_exists("Launcher.json")) {
			$this::setting();
		}
		
		// ��ȡ����������
		$launcher = json_decode(file_get_contents("Launcher.json"), true);
		if(!file_exists(ROOT . "/.minecraft/versions/" . $launcher['version'] . "/" . $launcher['version'] . ".jar")) {
			$this::logs("�ͻ��˲�������������������ؿͻ��ˡ�");
		}
		$forgejson = @file_get_contents(ROOT . "/.minecraft/versions/" . $launcher['version'] . "/" . $launcher['version'] . ".json");
		if(empty($forgejson)) {
			$this::logs("Json �ļ���ʧ���𻵣��볢���������ؿͻ��ˡ�");
			$this::shutdown(1);
		} else {
			$this::logs('Loading Libraries...');
			$readforgejson = json_decode($forgejson, true);
			$libList2 = "";
			$libVersion = $launcher['version'];
			$this->mainClass = $readforgejson['mainClass'];
			if(!isset($readforgejson['minecraftArguments'])) {
				$this->minecraftArguments = "";
				foreach($readforgejson['arguments']['game'] as $args) {
					if(is_string($args)) {
						$this->minecraftArguments .= $args . " ";
					}
				}
			} else {
				$this->minecraftArguments = $readforgejson['minecraftArguments'];
			}
			$assetsIndex = $readforgejson['assets'];
			foreach($readforgejson['libraries'] as $libforge) {
				$this::logs("Loading libraries: " . $libforge['name']);
				$libList2 .= ROOT . "/.minecraft/libraries/" . $this::get_lib_path($libforge['name']) . ";";
				usleep(20000);
			}
			$libList = $libList2;
			if(isset($readforgejson["jar"])) {
				$json = @file_get_contents(ROOT . "/.minecraft/versions/" . $readforgejson["jar"] . "/" . $readforgejson["jar"] . ".json");
				if(empty($json)) {
					$this::logs("Json �ļ���ʧ���𻵣��볢���������ؿͻ��ˡ�");
					$this::shutdown(1);
				} else {
					$readjson = json_decode($json, true);
					$assetsIndex = $readjson['assets'];
					$libVersion = $readforgejson["jar"];
					if(!file_exists(ROOT . "/.minecraft/libraries/")) {
						@mkdir(ROOT . "/.minecraft/libraries/");
					}
					foreach($readjson['libraries'] as $lib) {
						if(!isset($lib['downloads']['artifact'])) {
							continue;
						}
						$this::logs("Loading libraries: " . $lib['name']);
						if(!file_exists(ROOT . "/.minecraft/libraries/" . $lib['downloads']['artifact']['path'])) {
							// ����֧�ֿ��ļ�
							$this::logs($lib['name'] . " Not Found, downloading...");
							$librarie = @file_get_contents($lib['downloads']['artifact']['url']);
							if(!empty($librarie)) {
								@mkdir(ROOT . "/.minecraft/libraries/" . $this::get_dir($lib['downloads']['artifact']['path']), 0777, true);
								@file_put_contents(ROOT . "/.minecraft/libraries/" . $lib['downloads']['artifact']['path'], $librarie);
								$this::logs('Successful download librarie: ' . $lib['name']);
							} else {
								$this::logs('Failed to download librarie: ' . $lib['name'], 'ERROR');
							}
						} else {
							$libList .= ROOT . "/.minecraft/libraries/" . $lib['downloads']['artifact']['path'] . ";";
						}
						usleep(20000);
					}
				}
			}
		}
		
		// ������� UUID �� Token
		$uuid = md5(rand(0, 99999) . time() . microtime());
		$token = md5(rand(0, 99999) . time() . microtime());
		$userType = "Legacy";
		
		// �����¼
		if($launcher['user'] !== "" && $launcher['user'] !== "#" && $launcher['user'] !== null && $launcher['pass'] !== null) {
			$this::logs("Try connect to Mojang auth server...");
			$post_data = json_encode(Array(
				'agent' => Array(
					'name' => 'Minecraft',
					'version' => 1
				),
				'username' => $launcher['user'],
				'password' => $launcher['pass']
			));
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://authserver.mojang.com/authenticate');
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, Array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($post_data)
			));
			$data = curl_exec($curl);
			if (curl_errno($curl)) {
				$this::logs('Errno' . curl_error($curl));
			}
			curl_close($curl);
			$online_info = @json_decode($data, true);
			if(!$online_info) {
				$this::logs("Failed login to Mojang auth server");
				sleep(3);
				$this::home();
			}
			if(isset($online_info['error'])) {
				$this::logs($online_info['errorMessage']);
				sleep(3);
				$this::home();
			}
			$this->global_user = $online_info['selectedProfile']['name'];
			$uuid = $online_info['selectedProfile']['id'];
			$token = $online_info['accessToken'];
			$this::logs("Login successful, username: " . $this->global_user . ", uuid: " . $uuid);
			$userType = "Mojang";
		}
		
		// �滻��������
		$libList .= ROOT . "/.minecraft/versions/" . $libVersion . "/" . $libVersion . ".jar " . $this->mainClass;
		$this->minecraftArguments = str_replace('${auth_player_name}', $this->global_user, $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${version_name}', '"KasuganoSora"', $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${game_directory}', ROOT . '/.minecraft/', $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${assets_root}', ROOT . '/.minecraft/assets/', $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${assets_index_name}', $assetsIndex, $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${auth_uuid}', $uuid, $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${auth_access_token}', $token, $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${user_properties}', '{}', $this->minecraftArguments);
		$this->minecraftArguments = str_replace('${user_type}', $userType, $this->minecraftArguments);
		$cmdjava = str_replace("\\", "/", $launcher['java']);
		if($launcher['java'] == 'java') {
			$cmdjava = 'java';
			if(ISWIN) {
				$cmdjava = 'javaw';
			}
		}
		if(ISWIN) {
			$command = $cmdjava . " -XX:HeapDumpPath=MojangTricksIntelDriversForPerformance_javaw.exe_minecraft.exe.heapdump -XX:+UseG1GC "
			."-XX:-UseAdaptiveSizePolicy -XX:-OmitStackTraceInFastThrow -Xmn128m -Xmx" . $launcher['ram'] . "m -Djava.library.path=" . ROOT . "/.minecraft/versions/KasuganoSora/KasuganoSora-natives"
			. " -Dfml.ignoreInvalidMinecraftCertificates=true -Dfml.ignorePatchDiscrepancies=true -cp " . $libList . " " . $this->minecraftArguments;
		} else {
			$command = $cmdjava . " -Dminecraft.client.jar=" . ROOT . "/versions/" . $launcher['version'] . "/" . $launcher['version'] 
			. ".jar -Dminecraft.launcher.version=7.3.1031 \"-Dminecraft.launcher.brand=Sakura Minecraft Launcher\" -Xincgc -XX:-UseAdaptiveSizePolicy "
			. "-XX:-OmitStackTraceInFastThrow -Xmn128m -Xmx" . $launcher['ram'] . "m -Djava.library.path=" . ROOT . "/versions/" . $launcher['version'] . "/" . $launcher['version'] . "-natives "
			. "-Dfml.ignoreInvalidMinecraftCertificates=true -Dfml.ignorePatchDiscrepancies=true -Duser.home=null -cp " . $libList . " " . $this->minecraftArguments;
		}
		// $LoginService = new LoginService($this->global_user, $this->global_pass);
		// $LoginService->start();
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "r")
		);
		$process = proc_open($command, $descriptorspec, $pipes);
		if(is_resource($process)) {
			while(!feof($pipes[1])) {
				echo fread($pipes[1], 65535);
				echo fread($pipes[2], 65535);
			}
			fclose($pipes[1]);
			fclose($pipes[0]);
			fclose($pipes[2]);
			$return_value = proc_close($process);
		}
		$this::logs("��Ϸ��ֹͣ������ֵ��" . $return_value);
		if($return_value !== 0) {
			$this::logs("��Ϸ�쳣�˳�������ֵ��" . $return_value);
			$this::logs("�볢��������������ѯ���˽�����⡣");
		}
		sleep(2);
		$this::shutdown(0);
		
	}
	
	// ��������������
	public function shutdown($status) {
		
		$this::logs("��������ط�����ֹͣ...");
		exit($status);
		
	}
	
	// ���� Librarie ����ȡĿ¼��
	public function get_dir($path) {
		
		$ex = explode('/', $path);
		$rs = "";
		for($i = 0;$i < count($ex) -1;$i++) {
			$rs .= $ex[$i] . "/";
		}
		return $rs;
		
	}
	
	// ���� Librarie ����ȡ�ļ���
	public function get_lib_path($name) {
		
		$ex = explode(':', $name);
		$rs = str_replace('.', '/', $ex[0]) . '/';
		$rs .= $ex[1] . '/' . $ex[2] . '/' . $ex[1] . '-' . $ex[2] . '.jar';
		return $rs;
		
	}
	
}

// �Զ���¼��Ϸ֧�ֿ�
// KasuganoSora ������ר��
class LoginService extends Thread {
	
	public function __construct($user, $pass){
        $this->user = $user;
		$this->pass = $pass;
		$this->status = true;
    }
	
    public function run() {
		
		$SoraOS = new SoraOS();
		$SoraOS->logs("Starting Auto Login Service...");
		$SoraOS->logs("Set Login Username: " . $this->user);
		if(!file_exists(".minecraft/SoraLoginClient.jar")) {
			$SoraOS->logs("Failed: Auto login libraries not found!");
			return;
		}
        $descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("pipe", "r")
		);
		$process = proc_open("java -jar \".minecraft/SoraLoginClient.jar\" " . $this->user . " " . $this->pass, $descriptorspec, $pipes);
		while(!feof($pipes[0])) {
			//$SoraOS->logs(fread($pipes[1], 65535));
			//$SoraOS->logs(fread($pipes[2], 65535));
		}
		fclose($pipes[1]);
		fclose($pipes[0]);
		fclose($pipes[2]);
		$return_value = proc_close($process);
		$SoraOS->logs("Server Login Successful");
		
    }
}
// �������߳�
$SoraOS = new SoraOS();
$SoraOS->onEnable();