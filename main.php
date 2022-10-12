<?php 

    class bannerAppBacked{
        private $DB;
        private $arAvailableBanners=["1.jpg", "2.jpg", "elephant.jpg", "4.jpg"];
        private $arAvailableAPIActions=["getNewBannerSrc","getBannerStats","increaseBannerCounter"];

        public function __construct(){ 
            try {
                $this->DB = new PDO('mysql:host=localhost;dbname=infuse', 'root', 'root');
            } catch (PDOException $e) {
                $this->error($e->getMessage());               
            } 
            $this->route(); 
        }

        function route(){           
            $request = json_decode(file_get_contents('php://input'), true);    
            if (!empty($request)){
                if (in_array($request['action'],$this->arAvailableAPIActions)){
                   
                    if ($request['action']=="getNewBannerSrc"){
                        $this->returnRandomBannerSrc();
                    }
                    if ($request['action']=="getBannerStats"){
                        $this->returnBannerStats($request['src']);
                    }
                    if ($request['action']=="increaseBannerCounter"){
                        $this->increaseBannerCounter($request['src']);
                    }

                }
            } 
            $this->error("Wrong API action");  
        }

        function returnRandomBannerSrc(){
            $bannerIndex=rand(0,count($this->arAvailableBanners)-1);
            $this->reply([
                "src"=>$this->arAvailableBanners[$bannerIndex]
            ]);
        }

        function getBannerCounterValue($src=""){
            $counterValue=0;
            try{
                $query=$this->DB->prepare("SELECT `view_count` FROM `logs` WHERE `image_id`=:image_id ORDER BY `view_date` DESC LIMIT 1");
                $query->execute([
                    "image_id"=>$src
                ]);                
                $counterDbValue=$query->fetchColumn();
                $counterValue=$counterDbValue ? $counterDbValue : 0;
            }catch(Error $e){
                $this->error($e->getMessage());
            }
            return $counterValue;
        }

        function returnBannerStats($src=""){ 
            $this->reply([
                "counterValue"=>$this->getBannerCounterValue($src)
            ]);
        }

        function increaseBannerCounter($src=""){
            $currentCounterValue=$this->getBannerCounterValue($src);
            try{
                
                $query=$this->DB->prepare("INSERT INTO `logs` (`image_id`,`ip_address`,`user_agent`,`view_date`,`view_count`) VALUES (:image_id, :ip_address, :user_agent, :view_date, :view_count)");
                $query->execute([
                    "image_id"=>$src,
                    "ip_address"=>$_SERVER['REMOTE_ADDR'],
                    "user_agent"=>$_SERVER['HTTP_USER_AGENT'],
                    "view_date"=>date("Y-m-d H:i:s"),
                    "view_count"=>++$currentCounterValue
                ]);
            }catch(Error $e){
                $this->error($e->getMessage());
            }
        }

        function error($sMessage="Unknown Error"){
            $this->reply([
                'error'=>"Backend error ".$sMessage
            ]);
        }

        function reply($arResponceData=[]){
            ob_clean();
            header('Content-Type: application/json; charset=utf-8');
            die(json_encode($arResponceData,JSON_FORCE_OBJECT));
        }
    }

    $obApp=new bannerAppBacked();
    
?>