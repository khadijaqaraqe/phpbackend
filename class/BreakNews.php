
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';

$database = new Database();

$db = $database->getConnection();

class BreakNews {

    private $dbHost  = 'localhost';
    private $dbUser  = 'gcc10_kqaraqe';
    private $dbPwd   = 'N2c-v}fSq(,$';
    private $dbName  = 'gcc10_BethlehemGov';             
    private $db      = false;
    private $newsTbl = 'news';
    private $articlesTbl = 'articles';
    private $department_articlesTbl = 'department_articles';
    public function __construct(){
        if(!$this->db){ 
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUser, $this->dbPwd, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            } else {
                $this->db = $conn;
            }
        }
    }
    
    /*
     * Runs query to the database
     * @param string SQL
     * @param string count, single, all
     */
    private function getQuery($sql,$returnType = ''){
        $result = $this->db->query($sql);
        if($result){
            switch($returnType){
                case 'count':
                    $data = $result->num_rows;
                    break;
                case 'single':
                    $data = $result->fetch_assoc();
                    break;
                default:
                    if($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()){
                            $data[] = $row;
                        }
                    }
            }
        }
        return !empty($data)?$data:false;
    }
    
    /*
     * Get news result
     * @param news ID
     */
    public function getNews() {
       // $resultData = array();
            $sql = "SELECT `".$this->newsTbl."`.`id` AS ID,  `".$this->newsTbl."`.`text` AS Text, `".$this->newsTbl."`.`creator`, `".$this->newsTbl."`.`modified` as Modified,  `".$this->newsTbl."`.`created` as Created 
            FROM `".$this->newsTbl."`
            UNION 
            SELECT `".$this->articlesTbl."`.`id` AS ID, `".$this->articlesTbl."`.`title` AS Text, `".$this->articlesTbl."`.`creator`, `".$this->articlesTbl."`.`modified_date` as Modified , `".$this->articlesTbl."`.`created_date`as Created
            FROM `".$this->articlesTbl."`
            UNION 
            SELECT `".$this->department_articlesTbl."`.`id` AS ID, `".$this->department_articlesTbl."`.`title` AS Text, `".$this->department_articlesTbl."`.`creator`, `".$this->department_articlesTbl."`.`modified_date` as Modified , `".$this->department_articlesTbl."`.`created_date`as Created
            FROM `".$this->department_articlesTbl."`
            ORDER BY Modified DESC, Created DESC;";
            $newsResult = $this->getQuery($sql);
           /*  if(!empty($newsResult)){
                $resultData['Text'] = $newsResult['Text'];
                return $resultData;
            } */
        return $newsResult;//!empty($resultData)?$resultData:false;
    }

    public function getBreakNews() {
       // $resultData = array();
            $sql = "SELECT * 
            FROM `".$this->newsTbl."`
            ORDER BY modified DESC, created DESC;";
            $newsResult = $this->getQuery($sql);
           /*  if(!empty($newsResult)){
                $resultData['Text'] = $newsResult['Text'];
                return $resultData;
            } */
        return $newsResult;//!empty($resultData)?$resultData:false;
    }
    /*
     * Get news result
     * @param news ID
     */
    public function getOneNews($data = array()) {
        
        $sql = "SELECT * FROM `".$this->newsTbl."` WHERE `".$this->newsTbl."`.`id` = '".$data['id']."';";
            //$sql = "SELECT * FROM `".$this->newsTbl."` ORDER BY `".$this->newsTbl."`.modified DESC, `".$this->newsTbl."`.created DESC;";
            $newsResult = $this->getQuery($sql);
           /*  if(!empty($newsResult)){
                $resultData['Text'] = $newsResult['Text'];
                return $resultData;
            } */
        return $newsResult;//!empty($resultData)?$resultData:false;
    }

    /*
     * create news
     * @data news subject and options (accepted three options)
     */
    public function createNews($data = array()){
        try { 
            $uuid = "breaknews-".uniqid(date("Y").date("n").'0');
           //$uuid = "breaknews-".uniqid(date("Y").date("n").'0');
            $query = "INSERT INTO `".$this->newsTbl."` (`id`, `text`, `creator`) VALUES ('".$uuid."', '".htmlspecialchars(strip_tags($data['Text']))."', ".htmlspecialchars(strip_tags($data['Creator'])).");";
            $insert = $this->db->query($query);
            return true;
        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        } 
    }

     /*
     * update news 
     * @data news subject and options (accepted three options)
     */
    public function updateNews($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
           $query = "UPDATE ".$this->newsTbl." SET `text` =  '".htmlspecialchars(strip_tags($data['Text']))."' WHERE ".$this->newsTbl.".`id` = '".$data['id']."';";
           $update = $this->db->query($query);
          
           return true;

       } catch (mysqli_sql_exception $e) { 
           var_dump($e);
       } 
   }

   //DELETE FROM news WHERE `news`.`id` = 10

   public function deleteNews($data = array()) {
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
        $query = "DELETE FROM " .$this->newsTbl. " WHERE `".$this->newsTbl."`.`id` ='".htmlspecialchars(strip_tags($data['id']))."';";
        $delete = $this->db->query($query);
        
        return true;

    } catch (mysqli_sql_exception $e) { 
        var_dump($e);
    } 
    }

}
?>