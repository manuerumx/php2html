<?php
/**
 * Class used for handle MySQL connections
 * For servers using versions 4.01 or higher is recommended 
 * that you use the mysqli class. 
 * @see mysqli.class.php
 * @package PHP2HTML
 * @subpackage databases
 * @version    1.0 BETA
 * @author MANUEL GONZALEZ RIVERA <phptohtml@gmail.com>
 * @copyright Copyright (R) 2012, MANUEL GONZALEZ RIVERA <phptohtml@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 *  
 */
/**
 * SQLFunctions class is needed
 * @see SQLFunctions
 */
require_once 'sqlfunctions.class.php';
/**
 * Class used for handle MySQL connections
 * For servers using versions 4.01 or higher is recommended 
 * that you use the mysqli class. 
 * See mysqli.class.php
 * 
 * 
 * @package PHP2HTML
 * @subpackage databases
 * @version    1.0
 * @author MANUEL GONZALEZ RIVERA <phptohtml@gmail.com>
 * @copyright Copyright (R) 2012, MANUEL GONZALEZ RIVERA <phptohtml@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 *  
 */
class mysqlConn extends SQLFunctions{
    /**
     * Initialize the class
     * 
     * @param $obj object
     * @param $host string
     * @param $database string
     * @param $user string
     * @param $password string
     * @param $persistant boolean
     */
    public function __construct($obj, $host =DB_HOST, $database = DB_DATABASE, $user = DB_USER, $password = DB_PASS, $persistant = DB_PERSIST) {
        $this->HtmlC = $obj; 
        $this->host = $host;
        $this->database = $database;
        $this->user = $user;
        $this->password = $password;
        $this->persistant = $persistant;
        $this->Conn();
     }
     
     /**
     * Open the connection
     * 
     */
     protected function Conn() {
        if($this->persistant==true){
             $this->conn = mysql_pconnect($this->host, $this->user, $this->password);  
         }else{                 
             $this->conn = mysql_connect($this->host, $this->user, $this->password);                 
        }
        if(!$this->conn){                     
            $this->HtmlC->display_error('mysqlConn:Conn()', mysql_error());
        }else{
            if(!mysql_select_db($this->database, $this->conn)){
                $this->HtmlC->display_error('mysqlConn:Conn()', mysql_error());
            }
        }                
     }
     /**
     * Get the connection info
     * 
     * return string
     */
     public function ConnSummary() {
         return $this->user.'@'.$this->database.':'.$this->host;  
     }
     /**
      * Returns an associative array with the following record
      * 
      * @param boolean $assoc
      * @return mixed
      */
     public function Fetch($assoc = true){
         if($assoc == true){
            $this->row = mysql_fetch_assoc($this->rs);
         }else{
            $this->row = mysql_fetch_array($this->rs);
         }
         return is_array($this->row);
     }
    /**
    * Try running a SQL query. If the parameter is empty, it takes the variable $this->sql
    *
    * @param string $sql Sql Query
    */
     public function Query($sql = '') {
         if(is_resource($this->rs)) {
             mysql_free_result($this->rs);
         }
         $this->sql = ($sql=="" ? $this->sql : $sql);        
         $this->rs = mysql_query($this->sql, $this->conn);

     }
    /**
    * Move the pointer to the index consultation indicated
    *
    * @param int $num
    * @return boolean
    */
     public function Seek($num = 0){
         if(!empty($this->rs)) {
             $this->row = mysql_data_seek($this->rs, $num);            
             return true;
         }
         return false;
     }
    /**
    * Returns the number of rows affected by a query update, insert or delete
    *
    * @return int
    */
     public function affectedRows() {
         if(!empty($this->rs)){
             return mysql_affected_rows($this->conn);        
         }else{
             return 0;
         }
     }
    /**
    * Return the last id inserted
    *
    * @return int
    */
     public function getInsertedId() {
         if(!empty($this->rs)){
             return mysql_insert_id($this->conn);
         }else{
             return 0;
         }
     }
    /**
    * Returns the number of columns in the query result
    *
    * @return int
    * @throws ExceptionRecorset
    */
     public function numColumns() {
         if(!empty($this->rs)){
             return mysql_num_fields($this->rs);        
         }else{
             return 0;
         }            
     }
    /**
    * Returns the number of rows for a SELECT query resolved
    *
    * @return int    
    */
     public function numRows() {
         if(!empty($this->rs)){
             return mysql_num_rows($this->rs);        
         }else{
             return 0;
         }
     }
     /**
     * Destroy
     */
     public function __destruct() {
         $this->Close();
     }
     /**
     * Close the connection
     */
     public function Close() {
         $type = (is_resource($this->conn) ? get_resource_type($this->conn) : "none");
         if(strstr($type,"mysql")){        
             mysql_close($this->conn);            
         }else{                       
             if($type!='Unknown'){
                 //
             }
         }
     }
}
?>