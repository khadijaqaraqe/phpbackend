
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';

$database = new Database();

$db = $database->getConnection();

class Publications {
    public $id;  
    public $Creator;  
    public $Title;
    public $attachmentID= array();
    public $fileId;
    public $Path; 
    public $Type;

    private $dbHost  = 'localhost';
    private $dbUser  = 'gcc10_kqaraqe';
    private $dbPwd   = 'N2c-v}fSq(,$';
    private $dbName  = 'gcc10_BethlehemGov';             
    private $db      = false;
    private $reportsTbl = 'Reports';
    private $plansTbl = 'Plan';
    private $magazineTbl = 'Magazine';
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
                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            $data[] = $row;
                        }
                    }
            }
        }
        return !empty($data)?$data:false;
    }
    /*
     * REPORT QUERIES 
     * 
     */

    /*
     * Get reports result
     * 
     */
     public function getReports() {
        $resultData = array();
            $sql = "SELECT * FROM `".$this->reportsTbl."` ORDER BY `".$this->reportsTbl."`.modified DESC, `".$this->reportsTbl."`.created DESC;";
            $reportsResult = $this->getQuery($sql);
          
        return $reportsResult;//!empty($resultData)?$resultData:false;
    } 

    /*
     * Get report result - one report
     * @param report ID
     */
     public function getOneReport($data) {
        
        $sql = "SELECT * FROM `".$this->reportsTbl."` WHERE `".$this->reportsTbl."`.`id` = ".$data.";";
           
            $reportResult = $this->getQuery($sql);
          
        return $reportResult; //!empty($resultData)?$resultData:false;
    } 

    /*
     * create report
     * @data report subject and options (accepted three options)
     */
    public function createReport($data = array()){

         try { 
            $query = "INSERT INTO `".$this->reportsTbl."` ( `Title`, `Path`, `Creator`) VALUES ('".htmlspecialchars(strip_tags($data['Title']))."', '".htmlspecialchars(strip_tags($data['Path']))."', ".htmlspecialchars(strip_tags($data['Creator'])).");";
            $insert = $this->db->query($query);
            
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        }  
    }

     /*
     * update report 
     * @data report subject and options (accepted three options)
     */
     public function updateReport($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
           $query = "UPDATE ".$this->reportsTbl." SET `Title` =  '".htmlspecialchars(strip_tags($data['Title']))."' WHERE ".$this->reportsTbl.".`id` = ".$data['id'].";";
           $update = $this->db->query($query);
          
           return true;

       } catch (mysqli_sql_exception $e) { 
           var_dump($e);
       } 
   } 

   //DELETE FROM report WHERE `report`.`id` = 10

   public function deleteReport($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
            $query = "DELETE FROM " .$this->reportsTbl. " WHERE `".$this->reportsTbl."`.`id` =".htmlspecialchars(strip_tags($data['id'])).";";
            $delete = $this->db->query($query);
            
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        } 
    } 

    /*
     * PLAN QUERIES 
     * 
     */

    /*
     * Get plan result
     * 
     */
    public function getPlan() {
        $resultData = array();
            $sql = "SELECT * FROM `".$this->plansTbl."` ORDER BY `".$this->plansTbl."`.modified DESC, `".$this->plansTbl."`.created DESC;";
            $reportsResult = $this->getQuery($sql);
          
        return $reportsResult;//!empty($resultData)?$resultData:false;
    } 
    /*
     * Get report result - one report
     * @param report ID
     */
     public function getOnePlan($data) {
        
        $sql = "SELECT * FROM `".$this->plansTbl."` WHERE `".$this->plansTbl."`.`id` = ".$data.";";
           
            $reportResult = $this->getQuery($sql);
          
        return $reportResult; //!empty($resultData)?$resultData:false;
    } 

    /*
     * create report
     * @data report subject and options (accepted three options)
     */
    public function createPlan($data = array()){

         try { 
            $query = "INSERT INTO `".$this->plansTbl."` (`Title`, `Path`, `Creator`) VALUES ( '".htmlspecialchars(strip_tags($data['Title']))."', '".htmlspecialchars(strip_tags($data['Path']))."', ".htmlspecialchars(strip_tags($data['Creator'])).");";
            $insert = $this->db->query($query);
            
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        }  
    }

     /*
     * update report 
     * @data report subject and options (accepted three options)
     */
     public function updatePlan($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
           $query = "UPDATE ".$this->plansTbl." SET `Title` =  '".htmlspecialchars(strip_tags($data['Title']))."' WHERE ".$this->plansTbl.".`id` = ".$data['id'].";";
           $update = $this->db->query($query);
          
           return true;

       } catch (mysqli_sql_exception $e) { 
           var_dump($e);
       } 
   } 

   //DELETE FROM report WHERE `report`.`id` = 10

   public function deletePlan($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
            $query = "DELETE FROM " .$this->plansTbl. " WHERE `".$this->plansTbl."`.`id` =".htmlspecialchars(strip_tags($data['id'])).";";
            $delete = $this->db->query($query);
            
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        } 
    } 

    /*
     * MAGAZINE QUERIES 
     * 
     */

    /*
     * Get reports result
     * 
     */
    public function getMagazine() {
        $resultData = array();
            $sql = "SELECT * FROM `".$this->magazineTbl."` ORDER BY `".$this->magazineTbl."`.modified DESC, `".$this->magazineTbl."`.created DESC;";
            $reportsResult = $this->getQuery($sql);
          
        return $reportsResult;//!empty($resultData)?$resultData:false;
    } 
    /*
     * Get report result - one report
     * @param report ID
     */
     public function getOneMagazine($data) {
        
        $sql = "SELECT * FROM `".$this->magazineTbl."` WHERE `".$this->magazineTbl."`.`id` = ".$data.";";
           
            $reportResult = $this->getQuery($sql);
          
        return $reportResult; //!empty($resultData)?$resultData:false;
    } 

    /*
     * create report
     * @data report subject and options (accepted three options)
     */
    public function createMagazine($data = array()){

         try { 
            $query = "INSERT INTO `".$this->magazineTbl."` (`Title`, `Path`, `Creator`) VALUES ( '".htmlspecialchars(strip_tags($data['Title']))."', '".htmlspecialchars(strip_tags($data['Path']))."', ".htmlspecialchars(strip_tags($data['Creator'])).");";
            $insert = $this->db->query($query);
            
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        }  
    }

     /*
     * update report 
     * @data report subject and options (accepted three options)
     */
     public function updateMagazine($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
           $query = "UPDATE ".$this->magazineTbl." SET `Title` =  '".htmlspecialchars(strip_tags($data['Title']))."' WHERE ".$this->magazineTbl.".`id` = ".$data['id'].";";
           $update = $this->db->query($query);
          
           return true;

       } catch (mysqli_sql_exception $e) { 
           var_dump($e);
       } 
   } 

   //DELETE FROM report WHERE `report`.`id` = 10

   public function deleteMagazine($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
            $query = "DELETE FROM " .$this->magazineTbl. " WHERE `".$this->magazineTbl."`.`id` =".htmlspecialchars(strip_tags($data['id'])).";";
            $delete = $this->db->query($query);
            
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        } 
    } 

}
?>