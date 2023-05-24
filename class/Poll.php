
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpbackend/config/database.php';

$database = new Database();

$db = $database->getConnection();

class Poll{

    private $dbHost  = 'localhost';
    private $dbUser  = 'gcc10_kqaraqe';
    private $dbPwd   = 'N2c-v}fSq(,$';
    private $dbName  = 'gcc10_BethlehemGov';             
    private $db      = false;
    private $pollTbl = 'polls';
    private $optTbl  = 'poll_options';
    private $voteTbl = 'poll_votes';
    
    public function __construct(){
        if(!$this->db){ 
            // Connect to the database
            $conn = new mysqli($this->dbHost, $this->dbUser, $this->dbPwd, $this->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            }else{
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
     * Get polls data
     * Returns single or multiple poll data with respective options
     * @param string single, all
     */
    public function getPolls($pollType = 'single'){
        $pollData = array();
        $sql = "SELECT * FROM ".$this->pollTbl." WHERE status = '1' ORDER BY created DESC";
        $pollResult = $this->getQuery($sql, $pollType);
        if(!empty($pollResult)){
            if($pollType == 'single'){
                $pollData['poll'] = [$pollResult];
                $sql2 = "SELECT * FROM ".$this->optTbl." WHERE poll_id = ".$pollResult['id']." AND status = '1'";
                $optionResult = $this->getQuery($sql2);
                $pollData['options'] = $optionResult;
            }else{
                $i = 0;
                foreach($pollResult as $prow){
                    $pollData[$i]['poll'] = $prow;
                    $sql2 = "SELECT * FROM ".$this->optTbl." WHERE poll_id = ".$prow['id']." AND status = '1'";
                    $optionResult = $this->getQuery($sql2);
                    $pollData[$i]['options'] = $optionResult;
                }
            }
        }
        return !empty($pollData)?$pollData:false;
    }
    
    /*
     * Submit vote
     * @param array of poll option data
     */
    public function vote($data = array()){
        
        if(!isset($data['poll_id']) || !isset($data['poll_option_id']) || isset($_COOKIE[$data['poll_id']])) {
            return false;
        } else { 
            $sql1 = "SELECT * FROM `".$this->pollTbl."` WHERE `".$this->pollTbl."`.`id` = ".htmlspecialchars(strip_tags($data['poll_id']))."";
            $isPoll = $this->getQuery($sql1, 'count');
            if($isPoll > 0){
                $sql = "SELECT * FROM ".$this->voteTbl." WHERE poll_id = ".htmlspecialchars(strip_tags($data['poll_id']))." AND poll_option_id = ".htmlspecialchars(strip_tags($data['poll_option_id']));
                $preVote = $this->getQuery($sql, 'count');
                if($preVote > 0) {
                    $query = "UPDATE ".$this->voteTbl." SET vote_count = vote_count+1 WHERE poll_id = ".htmlspecialchars(strip_tags($data['poll_id']))." AND poll_option_id = ".htmlspecialchars(strip_tags($data['poll_option_id']));
                    $update = $this->db->query($query);
                } else {
                    $query = "INSERT INTO ".$this->voteTbl." (poll_id,poll_option_id,vote_count) VALUES (".htmlspecialchars(strip_tags($data['poll_id'])).",".htmlspecialchars(strip_tags($data['poll_option_id'])).",1)";
                    $insert = $this->db->query($query);
                }
            } else {
                return false;
            }
            return true;
        }
        
    }
    
    /*
     * Get poll result
     * @param poll ID
     */
    public function getResult($pollID){
        $resultData = array();
       
        if(!empty($pollID)){
            $sql = "SELECT p.subject, SUM(v.vote_count) as total_votes FROM `".$this->voteTbl."` as v LEFT JOIN `".$this->pollTbl."` as p ON p.id = v.poll_id WHERE poll_id = ".htmlspecialchars(strip_tags($pollID));
            $pollResult = $this->getQuery($sql,'single');
            
            
            if(!empty($pollResult)){
                $resultData['poll'] = $pollResult['subject'];
                $resultData['total_votes'] = $pollResult['total_votes'];
                $sql2 = "SELECT o.id, o.name, v.vote_count FROM ".$this->optTbl." as o LEFT JOIN ".$this->voteTbl." as v ON v.poll_option_id = o.id WHERE o.poll_id = ".htmlspecialchars(strip_tags($pollID));
                $optResult = $this->getQuery($sql2);
                if(!empty($optResult)){
                    foreach($optResult as $orow){
                        $resultData['options'][$orow['name']] = $orow['vote_count']; 
                    }
                }
            }
        }
        return !empty($resultData)?$resultData:false;
    }


    /*
     * create poll 
     * @data poll subject and options (accepted three options)
     */
    public function createPoll($data = array()){
         try { 
            $query = "INSERT INTO ".$this->pollTbl." ( `subject`, `status`) VALUES ( '".htmlspecialchars(strip_tags($data['subject']))."', 1)";
            $insert = $this->db->query($query);
            $last_id = $this->db->insert_id;
            $query2  = " INSERT INTO ".$this->optTbl." ( `poll_id`, `name`, `status`) VALUES
                ( ".$last_id.", '". htmlspecialchars(strip_tags($data['options'][0]["name"]))."', '1'),
                ( ".$last_id.", '". htmlspecialchars(strip_tags($data['options'][1]["name"]))."', '1'),
                ( ".$last_id.", '". htmlspecialchars(strip_tags($data['options'][2]["name"]))."', '1');";
            $insert = $this->db->query($query2);
            return true;

        } catch (mysqli_sql_exception $e) { 
            var_dump($e);
        } 
    }

     /*
     * update poll 
     * @data poll subject and options (accepted three options)
     */
    public function updatePoll($data = array()){
        try { //$stmt = $this->conn->prepare("UPDATE `articles` SET `Title` = ?, `Text` = ? WHERE `articles`.`ID` = ?;");
           $query = "UPDATE ".$this->pollTbl." SET `subject` =  '".htmlspecialchars(strip_tags($data['poll']['subject']))."' WHERE ".$this->pollTbl.".`id` = ".$data['poll']['id'].";";
           $update = $this->db->query($query);
           $queryoption1  = "UPDATE ".$this->optTbl." SET `name` = '". htmlspecialchars(strip_tags($data['options'][0]["name"]))."' WHERE " .$this->optTbl.".`id` = " .$data['options'][0]["id"].";";
           $update = $this->db->query($queryoption1);
           $queryoption2  = "UPDATE ".$this->optTbl." SET `name` = '". htmlspecialchars(strip_tags($data['options'][1]["name"]))."' WHERE " .$this->optTbl.".`id` = " .$data['options'][1]["id"].";";
           $update = $this->db->query($queryoption2);
           $queryoption3  = "UPDATE ".$this->optTbl." SET `name` = '". htmlspecialchars(strip_tags($data['options'][2]["name"]))."' WHERE " .$this->optTbl.".`id` = " .$data['options'][2]["id"].";";
           $update = $this->db->query($queryoption3);
           return true;

       } catch (mysqli_sql_exception $e) { 
           var_dump($e);
       } 
   }
}
?>