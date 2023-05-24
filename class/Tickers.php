<?php
class Tickers{   
    private $articleTable = 'articles';
	private $articlesImagesTable = 'articles_images';
	private $ImagesTable = 'images';
	private $typesTable = 'types';
	private $usersTable = 'users';
    public $id;
    public $title;
    public $text;
    private $conn;
    public function __construct($db){
        $this->conn = $db;
    }	
	
	function readTickers(){	
		$stmt = $this->conn->prepare("SELECT `".$this->articleTable."`.id, `".$this->articleTable."`.title, `".$this->articleTable."`.text 
		FROM `".$this->articleTable."` LEFT JOIN `".$this->articlesImagesTable."` ON `".$this->articlesImagesTable."`.article_id = `".$this->articleTable."`.id LEFT JOIN `".$this->ImagesTable."` ON `".$this->articlesImagesTable."`.image_id = `".$this->ImagesTable."`.id, `".$this->typesTable."`, `".$this->usersTable."`   
		WHERE `".$this->articleTable."`.category = `".$this->typesTable."`.id AND `".$this->articleTable."`.creator = `".$this->usersTable."`.id 
		ORDER BY `".$this->articleTable."`.modified_date DESC, `".$this->articleTable."`.created_date DESC 
		LIMIT 15;");
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
}
?>