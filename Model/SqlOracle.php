<?php


class SqlOra extends PDO{
    
    private $conn;
    // public $conn;

    public function __construct(){

        $tns = "  
        (DESCRIPTION =
            (ADDRESS_LIST =
            (ADDRESS = (PROTOCOL = TCP)(HOST = 172.168.1.25)(PORT = 1521))
            )
            (CONNECT_DATA =
            (SERVICE_NAME = WINT)
            )
        )
            ";
        $db_username = "PARALELO";
        $db_password = "strw4mxk";

        $this->conn = new PDO("oci:dbname=".$tns/*.";charset=UTF8"*/,$db_username,$db_password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'");
    }


    public function testeParam(){
        $str = "begin kokar.integradora.importarpedido(1,TRUNC(SYSDATE), TRUNC(SYSDATE), 1, null, null, 150006638); end;";
        $stmt = $this->conn->prepare($str);
        $stmt->execute();
    }


    private function setParam($statement ,$key, $value){
        $statement->bindParam($key, $value);
    }

    private function setParams($statement, $parameters = array()){
        foreach ($parameters as $key => $value){
            $this->setParam($statement, $key, $value);
        }
    }

    public function query($rawQuery, $params = array()){
        $stmt = $this->conn->prepare($rawQuery);
        $this->setParams($stmt, $params);
        $stmt->execute();
        return $stmt;
    }

    public function select($rawQuery, $params = array()):array{
        $stmt = $this->query($rawQuery, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function select($rawQuery, $params = array()):array{
    //     $stmt = $this->query($rawQuery, $params);
    //     $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $retorno = [];
    //     if(sizeof($ret)>0){
    //         foreach($ret[0] as $key=>$val){
    //             array_push($retorno, array(utf8_encode($key)=>utf8_encode($val)));
    //         }
    //     }
    //     return $retorno;
    // }



    public function insert($query, $params){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return 'ok';
        }catch(PDOException $e) {
            return 'Erro: ' . $e->getMessage();
        }
    }

    public function insertDirect($query){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return 0;
        }catch(PDOException $e) {
            return 'Erro: ' . $e->getMessage();
        }
    }
    public function insertDirect2($query){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        }catch(PDOException $e) {
            return 'Erro: ' . $e->getMessage();
        }
    }

    public function delete($query, $params){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return 'ok';
        }catch(PDOException $e) {
            return 'Error: ' . $e->getMessage();
            return 'erro';
        }
    }

    public function update($query, $params){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return 'ok';
        }catch(PDOException $e) {
            return 'Error de Update: ' . $e->getMessage();
        }
    }

    public function update2($query, $params){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return  $stmt->rowCount();
        }catch(PDOException $e) {
            return 'Error de Update: ' . $e->getMessage();
        }
    }

    public function updateDirect($query){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        }catch(PDOException $e) {
            return ['Error de Update: ' => $e->getMessage()];
        }
    }

}

?>