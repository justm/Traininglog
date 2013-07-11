<?php
///Zakladna trieda Controller sluzi ako ovladac pre vsetky zobrazovane stranky aplikacie, konkrétne controller triedy dedia od tejto triedy
class Controller extends CController {
        
    
        /** @var string defaultny layout obsahu stranky definovany v subore views/layouts/content.php */
        public $layout='//layouts/content';
        
        /** @var array Položky menu - premenna vytvorena povodne frameworkom*/
	public $menu=array();
        
	/** @var array Breadcrumbs aktuálnej stránky - premenna vytvorena povodne frameworkom */
	public $breadcrumbs=array();
       
        /** @var LoginForm Premenna obsluhuje moznost prihlasenia na ktorejkolvek stranke */
        public $login;
        
        /** @var array Vlastna navigacia - vymena za povodne menu z framevorku ktoru pouzivalo javascript navyse*/
        private $navigation = array();
        
        ///Konstruktor triedy Controller 
        /**
         * Metoda je pretazena je kvoli inicializacii premennej $login
         * Naslednej je zavolany rodicovsky CController
         */
        public function __construct($id, $module = null) {
            $this->login = new LoginForm();
            parent::__construct($id, $module);
        }
        
        /// Metoda spracuje prihlasenie cez login formular zo stranky view/layouts/main.php
        public function LoginUser(){
              
                /** Zozbieranie zadaných údajov a prihlásenie */
		if(isset($_POST['LoginForm']))
		{
			$this->login->attributes=$_POST['LoginForm'];
			// Validacia prihlasenia a presmerovanie na pozadovanu stranku po prihlaseni
			if($this->login->validate() && $this->login->login()){
                            //Ak prichadza z domovskej stranky tak presmerovanie na Dashboard
                            if (Yii::app()->user->returnUrl == Yii::app()->homeUrl && Yii::app()->user->roles != 'admin')
                                $this->redirect(Yii::app()->params->homePath.'/'.Yii::app()->language.'/dashboard');
                            else //vsetci ostatni sa presmeruju tam kam pozaduju
                                $this->redirect(Yii::app()->user->returnUrl);
                        }
                        
				
		}
        }
        //Metoda pre zmenu jazyka
        public function actionChangelang(){
            
            if(isset($_POST['return'])){
                $return = substr($_POST['return'], strlen(Yii::app()->params->homePath));
                $this->redirect(Yii::app()->params->homePath.'/' . $_POST['lang'] . substr($return, 3, strlen($return)));
            }
        }

        /// Metóda pre vytvorenie jednourovnovej navigacie
        /**
         * @param array $nav Vstupne pole je vytvorene v scripte views/layouts/main
         */
        public function createNav($nav = array()){
             $this->navigation = $nav;
        }
        
        /// Metoda pre vypis navigacie v spravnom html formáte
        public function printNav(){
            echo '<nav><ul>';
            foreach ($this->navigation as $item){
                echo $item;
            }
            echo '</ul></nav>';
        }
        
        /// Metoda pre vytvorenie linku do menu
        /**
         * @param string $name nazov odkazu
         * @param string $title parameter tagu <a>
         * @param string $href parameter tagu <a> vo formate controller/action  
         * @return string $menu jeden odkaz v menu
         */
        public function createLink($name, $title='', $href='#', $li_class = null, $html_options=array()){
            if($href == '#'){
                $href = '#';
            }
            else{
                $href = Yii::app()->params->homePath . '/'.Yii::app()->language.'/' . $href ;
            }
            $menu = '';
            
            //Osetrenie ak su k linku priradene dve triedy
            if($li_class != null ){
                $menu = '<li class="'.$li_class; 
                if($href == Yii::app()->request->getUrl())
                    $menu .= ' active"'; 
                else{
                    $menu .= '"';
                }         
            }
            else{
                $menu = '<li ';
            }
            if($href == Yii::app()->request->getUrl()) //NASTAVENIE aktivneho  ak je pouzivatel na danej url
                $menu .= 'class="active"'; 
            
            $menu .= '><a href="'.$href.'" title="'.$title.'"';
            
            foreach ($html_options as $option=>$value){
                $menu .= $option.'="'.$value.'"';
            }
            $menu .='>'.$name.'</a>';
            
            return $menu;
        }

        /// Metoda pre ziskanie premennej $navigation
        /**
         * @return array $this->navigation
         */
        public function getNavigation() {
            return $this->navigation;
        }
        
        /// Metoda obsluhuje upload suboru na server do zložky uploads
        /**
         * @param string $id_form Used as attribute for $_FILE[name]
         */
        public function uploadProfilePricture($id_form='uploaded'){
                require_once('ImageManipulator.php');
                
                $allowedExts = array("jpg", "jpeg", "png","gif");
                $filename = explode('.',$_FILES[$id_form]["name"]);/*Rozbitie nazvu a pripony kvoli premenovavaniu suboru*/
                $filename[0] =  'user-'.Yii::app()->user->id.'-'.$this->randString(5);
                //$file_path = "http://".$_SERVER['SERVER_NAME'].Yii::app()->request->baseUrl."/uploads/profile-picture/". $filename[0] .'.'. $filename[1];
               
                if (((
                        $_FILES[$id_form]["type"] == "image/gif") || 
                        ($_FILES[$id_form]["type"] == "image/jpeg") || 
                        ($_FILES[$id_form]["type"] == "image/png") || 
                        ($_FILES[$id_form]["type"] == "image/pjpeg")
                        ) && in_array($filename[count($filename)-1], $allowedExts)) {
                    
                    if($_FILES[$id_form]["size"] > 1048576){ /*Subor ma viac ako 1MB*/
                        Yii::app()->user->setFlash('uploading-failed','The maximum picture size is 1024 kB. Your picture has '.(round(($_FILES[$id_form]["size"] / 1024))).' kB');
                        return null;
                    }
                    else{
                        $manipulator = new ImageManipulator($_FILES[$id_form]['tmp_name']);
                        $width  = $manipulator->getWidth();
                        $height = $manipulator->getHeight();
                        
                        //zmensenie obrazka na spravnu velkost
                        if($width>$height){
                            $newWidth = round($width*(200/$height));
                            $newImage = $manipulator->resample($newWidth, 200);
                        }
                        else{
                            $newHeight = round($height*(200/$width));
                            $newImage = $manipulator->resample(200, $newHeight);
                        }
                        
                        //orezanie obrazka na stvorec
                        $width  = $manipulator->getWidth();//Ziskanie novych velkosti
                        $height = $manipulator->getHeight();
                        
                        $centreX = round($width / 2);
                        $centreY = round($height / 2);
                        $x1 = $centreX - 100; 
                        $y1 = $centreY - 100; 
                        $x2 = $centreX + 100; 
                        $y2 = $centreY + 100; 

                        // center cropping to 200x200
                        $newImage = $manipulator->crop($x1, $y1, $x2, $y2);
                        
                        if(@$manipulator->save(Yii::app()->params->uploadDirectory."/profile-picture/" . $filename[0] .'.'. $filename[count($filename)-1])){
                            Yii::app()->user->setFlash('file-uploaded','Profile picture uploaded successfully.');
                            return  $filename[0] .'.'. $filename[count($filename)-1];
                        }
                        else{
                            Yii::app()->user->setFlash('uploading-failed','Upload failed '.$_FILES[$id_form]["error"]);
                            return null;
                        }
                    }
                }
                else{
                    Yii::app()->user->setFlash('uploading-failed','You have attempted to upload file type that is not allowed. Allowed file types: jpeg, jpg, png, gif');
                    return null;
                }
        }
        ///Metoda kontroluje či obrazok existuje
        /**
         * 
         * @param string $file_name Nazov hladaneho obrazku
         * @param string $folder Zlozka v ktorej sa vyhladava
         * @return string Cesta k suboru, null ak nebol najdeny
         */
        public function getPicture($file_name,$folder=''){
            if(file_exists(Yii::app()->params->uploadDirectory.'/'.$folder.'/'.$file_name)){
                return Yii::app()->request->baseUrl.'/uploads/'.$folder.'/'.$file_name;
            }
            else {   
                return null;            
            };
        }
        
        /** @var string Titulka stranky, pokial nie je definovana ina lokalne*/
        public $pageTitle = 'Online tréningový denník a tréningový plán | MojTrening.sk';
        /** @var string Description tag stranky pokial nie je definovany inak lokalne*/
        public $pageDesc = 'Osobný tréner pre Váš šport. Zlepšite svoj tréning a cvičenie už dnes s online tréningovým denníkom. Cvičte zdravo a s prehľadom.';
       
        /** @var string Titulka stranky pre zdielanie na socialnych sietach, premennej je priradena aktualna $pageTitle*/
        public $pageOgTitle = '';
        /** @var string Description tag stranky pre zdielanie na socialnych sietach, premennej je priradena aktualny $pageDesc*/
        public $pageOgDesc = '';
        /** @var string Thumb picture, obrazok pre zdielanie na socialnych sietach*/
        public $pageOgImage = '';
        
        ///Metoda pridava meta znacky do html potrebne pre facebookove zdielanie a Google indexovanie
        public function display_seo() {
            // STANDARD TAGS
            // Title/Desc
            echo "\t".''.PHP_EOL;
            echo "\t".'<meta name="description" content="',CHtml::encode($this->pageDesc),'">'.PHP_EOL;

            // OPEN GRAPH(FACEBOOK) META
            if ( !empty($this->pageOgTitle) ) {
                echo "\t".'<meta property="og:title" content="',CHtml::encode($this->pageOgTitle),'">'.PHP_EOL;
            }
            if ( !empty($this->pageOgDesc) ) {
                echo "\t".'<meta property="og:description" content="',CHtml::encode($this->pageOgDesc),'">'.PHP_EOL;
            }
            if ( !empty($this->pageOgImage) ) {
                echo "\t".'<meta property="og:image" content="',$this->pageOgImage,'">'.PHP_EOL;
            }
        }
        
        //Metoda generuje nahodny retazec znakov
        /**
         * 
         * @param string $amount dlzka retazca ktory ma byt vygenerovan
         * @return string $hash vygenerovany nahodny string pozadovanej dlzky
         */
        function randString($amount){ 
            
            $characters = 'abcdefghijklmnopqrstuv0123456789'; 
            $length_chracters = strlen($characters); 
            $length_chracters--; 

            $hash=NULL; 
                for($i=1;$i<=$amount;$i++){ 
                    $temp = rand(0,$length_chracters); 
                    $hash .= substr($characters,$temp,1); 
                } 

            return $hash; 
            } 

        /** @var array $days Dni v tyzdni*/
        protected $days = array();
        
        ///Metoda inincializuje premennu $this->days na zaklade aktualneho jazyka
        protected function translateDays(){
            $this->days = array(
                0=>Yii::t('calendar','Sunday'),
                1=>Yii::t('calendar','Monday'),
                2=>Yii::t('calendar','Thuesday'),
                3=>Yii::t('calendar','Wednesday'),
                4=>Yii::t('calendar','Thursday'),
                5=>Yii::t('calendar','Friday'),
                6=>Yii::t('calendar','Saturday'),
            );
        }
        
        ///Pomocna metoda ktora vytvara html element pre zobrazenie zdielanych treningovych zaznamov
        /**
         * @var $entry TrainingEntry jeden zaznam
         * @var $activity_array Array od Activity zoznam vsetkych aktivit z databazy
         */
        public function createSharedEntryDiv($entry,$activity_array){
                $i = 0;
                echo '<a class="shared-entry-small" id="'.$entry->id.'" 
                    href="'.Yii::app()->params->homePath.'/'.Yii::app()->language.'/workout/view/'.$entry->id.'">';

                if(isset($activity_array[$entry->id_activity])){
                    echo '<div class="single-day-activity" id="activity-'.$entry->id_activity.'">'.
                            Yii::t('activity',$activity_array[$entry->id_activity]->name).'</div>';
                }
                echo '<div class="shared-entry-date">'.date ('d. m. Y',strtotime($entry->date)).'</div>';
                echo '<div style="clear:both; margin-bottom:5px;"></div>';
                if($entry->distance != 0){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.$entry->getAttributeLabel('distance').'</span><br/>'.
                            '<span class="shared-entry-data">'.number_format($entry->distance, 1, '.', '').'</span> km</div>';
                    $i++;
                }
                if($entry->avg_speed != 0){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.$entry->getAttributeLabel('avg_speed').'</span><br/>'.
                            '<span class="shared-entry-data">'.number_format($entry->avg_speed, 1, '.', '').'</span> km/h</div>';
                    $i++;
                }
                elseif ($entry->avg_pace != '00:00:00' && $entry->avg_pace != null){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.$entry->getAttributeLabel('avg_pace').'</span><br/>'.
                            '<span class="shared-entry-data">'.date ('i:s',strtotime($entry->avg_pace)).'</span> min/km</div>';
                    $i++;
                }
                if($i<=1 && ($entry->avg_hr != null || $entry->max_hr != null)){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.Yii::t('diary','Heart rate').'</span><br/>'.'<span class="shared-entry-data">';
                    echo $entry->avg_hr? $entry->avg_hr : '--';
                    echo '/';
                    echo $entry->max_hr? $entry->max_hr : '--';
                    echo '</span> '.Yii::t('diary','bpm').'</div>';
                    $i++;
                }
                elseif($i>=2 && ($entry->avg_hr != null || $entry->max_hr != null)){
                    echo '<div class="shared-entry-hr-small">';
                    echo $entry->avg_hr? $entry->avg_hr : '--';
                    echo ' / ';
                    echo $entry->max_hr? $entry->max_hr : '--';
                    echo '</div>';
                }
                if($i<=1 && $entry->avg_watts){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.$entry->getAttributeLabel('avg_watts').'</span><br/>'.
                            '<span class="shared-entry-data">'.$entry->avg_watts.'</span> W</div>';
                    $i++;
                }
                if($i<=1 && $entry->ascent){
                    echo '<div class="shared-entry-data-wr"><span class="data-label">'.$entry->getAttributeLabel('ascent').'</span><br/>'.
                            '<span class="shared-entry-data">'.$entry->ascent.'</span> m</div>';
                    $i++;
                }

                echo '<div class="single-day-duration">'.date ('H:i',strtotime($entry->duration)).'</div>';

                echo '<div style="clear:both"></div>';

                echo '</a>';
        }
        
        //Metoda ulahcuje vykonanie požiadavky do databázy
        /**
         * @var $sqlStatement String Sql Query to execute
         * @return array of rows represented as objects
         */
        public function executeStringQuery($sqlStatement){

            $connection = Yii::app()->db;
            $connection->active = true;
            
            $command=$connection->createCommand($sqlStatement);
            $result=$command->query();

            $i=0; 
            $r_array = array();
            
            foreach ($result as $value){
                $r_array[$i++] = (object) $value;
            }
            
            return $r_array;
        }
        
        ///Pomocna metoda ktora vrati cast stringu, odstranenie bugu kodovania pri substr a nefunkcnosti mb_substr
        /**
         * @var $str String
         * @var $s Integer start position
         * @var $l Integer length
         */
        public function substr_unicode($str, $s, $l = null) {
            return join("", array_slice(
                preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
        }
}