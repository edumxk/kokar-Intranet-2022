
<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/modulos/modChamados/dao/daoRat.php');


require_once("produto.php");
require_once("despesa.php");
require_once("cliente.php");
require_once("aTec.php");
require_once("aLab.php");
require_once($_SERVER["DOCUMENT_ROOT"] . '/modulos/modChamados/dao/novaRatDao.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/modulos/modChamados/dao/daoCtd.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/modulos/modChamados/dao/ratProdDao.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/modulos/modChamados/controle/ratProdControle.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/modulos/modChamados/controle/corretivaControle.php');


class Chamado{
    public $numRat;
    public $dtAbertura;
    public $dtEncerramento;
    public $codCli;
    public $solicitante;
    public $telSolicitante;
    public $pintor;
    public $telPintor;
    public $problema;
    public $produtos = [];       //Array de produtos (tabela ratI)
    public $prodFinal;
    public $dtprodFinal;
    public $ALab;           //Tabela ratLab
    public $ALabFinal;
    public $dtALabFinal;
    public $corretiva;      //Tabela aCorretiva
    public $acaoFinal;
    public $dtacaoFinal;
    public $dirFInal;
    public $dtdirFinal;
    public $ATec;           //Char (P)endente, (S)im ou (N)ão para Parecer Técnico;
    public $ADir;           //Char (P)endente, (S)im ou (N)ão para Aprovação da Diretoria;
    //Inner join de outras tabelas
    public $codRca;
    public $rca;
    public $telRca;
    public $telCliente;
    public $cliente;
    public $cnpj;
    public $fantasia;
    public $cidade;
    public $uf;
    public $email;
    public $coduseraprova;
    public $useraprova;     //Usuário que aprovou a rat;
    public $aprovalab;      //Usuário que aprovou o parecer laboratórial rat;
    public $dtrejeicao;     //Data da Rejeição da diretoria;
    public $coduserrejeicao;//CodUsuário responsável pela rejeição;
    public $userrejeicao;   //Nome Usupario responsável pela rejeição;
    public $motivo;         //Motivo da Rejeição    
    //Valores Calculados
    public $pendencia;
    public $valor;          //Valor de devolução/ Diferente de valor de correção.


    public static function getListaRat(){
        $lista = Rat::getListaRat();

        $retorno = [];
        if(count($lista)>0){
            for($i = 0; $i< sizeof($lista); $i++){
                $row = $lista[$i];
                
                $rat = new Chamado();
                $rat->numRat = $row['NUMRAT'];
                $rat->dtAbertura = $row['DTABERTURA'];
                $rat->dtEncerramento = $row['DTENCERRAMENTO'];
                $rat->codCli = $row['CODCLI'];
                $rat->cliente = $row['CLIENTE'];
                $rat->prodFinal = $row['PRODFINAL'];
                $rat->alabFinal = $row['ALABFINAL'];
                $rat->acaoFinal = $row['ACAOFINAL'];
                $rat->dirFinal = $row['DIRFINAL'];
                $rat->ATec = $row['ATEC'];
                $rat->ADir = $row['ADIR'];
                $rat->pendencia();
                $rat->getValorTotal();

                array_push($retorno, $rat);
            }
        }
        return $retorno;
    }
    
    public static function getListaRatBusca($lote, $de, $ate){
        $lista = Rat::getListaRatBusca($lote, $de, $ate);

        $retorno = [];
        if(count($lista)>0){
            for($i = 0; $i< sizeof($lista); $i++){
                $row = $lista[$i];
                
                $rat = new Chamado();
                $rat->numRat = $row['NUMRAT'];
                $rat->dtAbertura = $row['DTABERTURA'];
                $rat->dtEncerramento = $row['DTENCERRAMENTO'];
                $rat->codCli = $row['CODCLI'];
                $rat->cliente = $row['CLIENTE'];
                $rat->prodFinal = $row['PRODFINAL'];
                $rat->alabFinal = $row['ALABFINAL'];
                $rat->acaoFinal = $row['ACAOFINAL'];
                $rat->dirFinal = $row['DIRFINAL'];
                $rat->ATec = $row['ATEC'];
                $rat->ADir = $row['ADIR'];
                $rat->pendencia();
                $rat->getValorTotal();

                array_push($retorno, $rat);
            }
        }
        return $retorno;
    }

    public static function getListaRatByCli($codcli){
        $lista = Rat::getListaRatByCli($codcli);

        $retorno = [];
        if(count($lista)>0){
            for($i = 0; $i< sizeof($lista); $i++){
                $row = $lista[$i];
                
                $rat = new Chamado();
                $rat->numRat = $row['NUMRAT'];
                $rat->dtAbertura = $row['DTABERTURA'];
                $rat->dtEncerramento = $row['DTENCERRAMENTO'];
                $rat->codCli = $row['CODCLI'];
                $rat->cliente = $row['CLIENTE'];
                $rat->prodFinal = $row['PRODFINAL'];
                $rat->alabFinal = $row['ALABFINAL'];
                $rat->acaoFinal = $row['ACAOFINAL'];
                $rat->dirFinal = $row['DIRFINAL'];
                $rat->ATec = $row['ATEC'];
                $rat->ADir = $row['ADIR'];
                $rat->patologia = utf8_encode($row['PATOLOGIA']);
                $rat->pendencia();
                $rat->getValorTotal();
                $rat->produtos = ratProdDao::getProdInRat($rat->numRat);

                array_push($retorno, $rat);
            }
        }
        return $retorno;
    }

    public function pendencia(){
        if($this->prodFinal == 'N'){
            $this->pendencia = 'PRODUTOS';
            //exit();
        }elseif($this->alabFinal == 'N'){
            $this->pendencia =  'LABORATÓRIO';
            //exit();
        }elseif($this->acaoFinal == 'N'){
            $this->pendencia =  'AÇÃO CORRETIVA';
            //exit();
        }elseif($this->dirFinal == 'N' && $this->ATec == 'S'){
            $this->pendencia =  'DIRETORIA';
            //exit();
        }elseif($this->ADir == 'S'){
            $this->pendencia = 'CTD';
            //exit()
        }else{
            $this->pendencia = 'FINALIZADO';
            //exit();
        }
    }

    public function getStatus(){
        if($this->ATec == 'P'){
            return 'PENDENTE';
        }elseif($this->ATec == 'S'){
            return 'PROCEDENTE';
        }elseif($this->ATec == 'N'){
            return 'NÃO PROCEDENTE';
        }
        elseif($this->ATec == 'C'){
            return 'CANCELADA';
        }
    }

    public function getValorTotal(){
        $vl = 0;
        for($i = 0; $i<sizeof($this->produtos); $i++){
            $prod = new Produto();
            $prod = $this->produtos[$i];
            $vl += $prod->getTotal();
        }
        return $vl;
    }

    public function getCorretivaTotal(){
        $ret = 0;
        foreach($this->corretiva as $c){
            $ret += $c->valor*$c->qt;
        }
        return $ret;
    }

    public static function getRat($numrat){
        if($numrat ==""){
            return null;
        }else{
            
            $chamado = Rat::getRat($numrat);

            if(sizeof($chamado)>0){
                $row = $chamado[0];
                
                $rat = new Chamado();
                $rat->numRat = $row['NUMRAT'];
                $rat->dtAbertura = $row['DTABERTURA'];
                $rat->dtEncerramento = $row['DTENCERRAMENTO'];
                $rat->codCli = $row['CODCLI'];
                $rat->solicitante = utf8_encode($row["SOLICITANTE"]);
                $rat->telSolicitante = $row["TEL_SOLICITANTE"];
                $rat->pintor = utf8_encode($row["PINTOR"]);
                $rat->telPintor = $row["TEL_PINTOR"];
                $rat->problema = utf8_encode($row["PROBLEMA"]);
                $rat->produtos = [];

                $prods = Rat::getProdRat($numrat);
                if(sizeof($prods)>0){
                    for($i = 0; $i < sizeof($prods); $i++){
                        $rowprod = $prods[$i];
    
                        $prod = new Produto();
                        $prod->codprod = $rowprod['CODPROD'];
                        $prod->produto = utf8_encode($rowprod['PRODUTO']);
                        $prod->numlote = $rowprod['NUMLOTE'];
                        $prod->dtFabricacao = $rowprod['DATAFABRICACAO'];
                        $prod->dtValidade = $rowprod['DTVALIDADE'];
                        $prod->pVenda = $rowprod['PVENDA'];
                        $prod->qt = $rowprod['QT'];
                        array_push($rat->produtos, $prod);
                    }
                }

                $rat->prodFinal = $row['PRODFINAL'];
                $rat->dtprodFinal = $row['DTPRODFINAL'];
                
                $al = new ALab();
                $al->getALab($numrat);
                
                $rat->ALab = $al;
                $rat->alabFinal = $row['ALABFINAL'];
                $rat->dtalabFinal = $al->data;
                $rat->aprovalab = $al->nome;
                $rat->corretiva = Corretiva::getCorretiva($numrat);
                $rat->acaoFinal = $row['ACAOFINAL'];
                $rat->dtacaoFinal = $row['DTACAOFINAL'];
                $rat->dirFinal = $row['DIRFINAL'];
                $rat->dtdirFinal = $row['DTDIRFINAL'];
                $rat->ATec = $row['ATEC'];
                $rat->ADir = $row['ADIR'];
                $rat->codRca = $row['CODUSUR'];
                $rat->rca = explode(' ',$row['NOME'])[0];
                $rat->telRca = $row['TELEFONE1'];
                $rat->telCliente = $row['TELENT'];
                $rat->cliente = $row['CLIENTE'];
                $rat->cnpj = $row['CGCENT'];
                $rat->email = $row['EMAIL'];
                $rat->fantasia = $row['FANTASIA'];
                $rat->cidade = $row['NOMECIDADE'];
                $rat->uf = $row['UF'];
                $rat->pendencia();
                $rat->coduseraprova = $row['USERAPROVA'];
                $rat->useraprova = $row['APROVACAO'];
                $rat->aprovalab = $row['APROVALAB'];
                $rat->dtrejeicao = $row['DTREJEICAO'];
                $rat->userrejeicao = $row['USERREPROVACAO'];
                $rat->motivo = $row['MOTIVO'];
                return $rat;
            }
        }
    }

    public function getClientes(){
        $clientes = Rat::getClientes();
        $ret = [];
        foreach($clientes as $c){
            $ret[] = new Cliente( ($c['CODCLI']), utf8_encode($c['CLIENTE']) );
        }
        return $ret;
    }

    public function getDespesas(){
        return Ctd::getDespesas($this->numRat);
    }

    public function getCtd(){
       return Ctd::getDadosCTD($this->numRat);
    }


}







?>