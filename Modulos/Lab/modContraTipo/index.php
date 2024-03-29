<!DOCTYPE html>

<?php 
require_once($_SERVER["DOCUMENT_ROOT"].'/Modulos/Lab/modContraTipo/control/controle.php');
session_start();
if($_SESSION['nome']== null){
    header("location:\..\..\home.php");
}
date_default_timezone_set('America/Araguaina');

$produtos = ProdutoPesquisa::getProduto();
?>
<html lang="pt-BR">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
        <title>Formulas - Alterar</title>
        <link rel="stylesheet" href="./src/css/reset.css">
        <link rel="stylesheet" href="./src/css/sidebar.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="./src/css/style.css">  
        <link rel="shortcut icon" type="image/x-icon" href="/Recursos/img/favicon.ico"> 
    </head>
    <body>
        <main>
            <section class="menu">
                <div class="logo-conteudo">
                    <div class="logo">
                        <img class='kokar_logo' src="/Recursos/src/Logo-kokar.png"></img>
                        <div class="logo-nome">Laboratório</div>
                    </div>
                    <i class='bx bx-menu' id="btn"></i>
                </div>
                <ul class="navegacao">
                    <li>
                        <a href="index.php">
                            <i class='bx bx-home'></i>
                            <span class="link-nome">Página Inicial</span>
                        </a>
                        <span class="tooltip">Página Inicial</span>
                    </li>
                    <li>
                        <a href="relatorio.php">
                            <i class='bx bx-message-rounded-edit'></i>
                            <span class="link-nome">Histórico</span>
                        </a>
                        <span class="tooltip">Histórico</span>
                    </li>
                    <li>
                        <a href="./../modRevalidacao/revalidacao.php">
                            <i class='bx bx-copy-alt'></i>
                            <span class="link-nome">Revalidar Lotes</span>
                        </a>
                        <span class="tooltip">Revalidar Lotes</span>
                    </li>
                    <li>
                        <a href="./../modMetodo/index.php">
                            <i class='bx bx-merge'></i>
                            <span class="link-nome">Copiar Fórmulas</span>
                        </a>
                        <span class="tooltip">Copiar Fórmulas</span>
                    </li>
                </ul>
                <div class="perfil-conteudo">
                    <div class="perfil">
                        <div class="perfil-detalhes">
                             <!--<img src="./src/img/curr.jpg" alt="imagem de perfil">-->
                             <div class="nome-setor">
                                <div class="nome"><?=$_SESSION['nome']?></div>
                                <div class="cargo"><?=$_SESSION['cargo']?></div>
                                <div class="setor"><?=$_SESSION['setor']?></div>
                            </div>
                        </div>
                        <a href="/homelab.php"><i class='bx bxs-log-out' id="sair"></i></a>
                    </div>
                </div>
            </section>
            <section class="home-conteudo"> 
            <datalist id="produtos-lista" name="produtos-lista">
                <?php foreach($produtos as $i):?>
                <option id="<?=$i->getCodprod()?>" value="<?=$i->getCodprod()?>"><?=$i->getDescricao()?></option>
                <?php array_push($produtos,[$i->getCodprod()=>$i->getDescricao()]);
                endforeach;?> 
            </datalist>
                <div class="formulario">     
                    <form action="contratipo.php" id="contratipo" method="POST"> 
                        <div class="titulo">
                            <span>Contratipo de Produtos</span>
                        </div>
                        <div class="formulario-itens-titulo">
                            <spam>Produto Origem</spam>
                        </div>
                        <div class="formulario-itens-codigo"> 
                            <label for="codigo1">Código</label> 
                            <input list="produtos-lista" required class="codigo" placeholder="Código..." type="text" id="codigo1" name="codigo1"> 
                            <input type="text" id="produto1" required placeholder="Digite o código do produto"  autocomplete="off" disabled> 
                        </div>
                        <div class="formulario-itens-titulo">
                            <spam>Produto Destino</spam>
                        </div>
                        <div class="formulario-itens-codigo">
                            <label for="codigo2">Código</label>
                            <input list="produtos-lista" required class="codigo" placeholder="Código..." type="text" id="codigo2" name="codigo2">
                            <input type="text" id="produto2" required placeholder="Digite o código do produto"  autocomplete="off" disabled>                        
                        </div>
                        <div class="formulario-itens-metodo">
                            <label class="formulario-itens-metodo-label" for="metodo">Método</label>
                            <select name="metodo" id="metodo">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                        <div class="formulario-botao">
                            <button onclick="return validar()" type="submit">Avançar</button>
                        </div>
                    </form>
                </div>
            </section>  
        </main>
        <script src="js/jquery.min.js"></script>
        <script src="./src/js/sidebar.js"></script>
        <script src="./src/js/index.js"></script>
        <script>
            window.onload = function() {
            let produto1 = $("#codigo1");
            let produto2 = $("#codigo2");
            let descricao1 = $("#produto1");
            let descricao2 = $("#produto2");

                produto1.on("keyup", function() {
                    let cod = produto1.val();
                    $.ajax({
                        type: 'POST',
                        url: './control/controle.php',
                        data: { 'action': 'getProduto', cod },
                        success: function (response) {
                         
                            if(response.length>0){
                                descricao1.val(response); 
                            }
                            else {
                                descricao1.val("CÓDIGO INVÁLIDO");
                            }
                        }		  
                    });
                })
                produto2.on("keyup", function() {
                    let cod = produto2.val();
                    $.ajax({
                        type: 'POST',
                        url: './control/controle.php',
                        data: { 'action': 'getProduto', cod },
                        success: function (response) {
                          
                            if(response.length>0){
                                descricao2.val(response); 
                            }
                            else {
                                descricao2.val("CÓDIGO INVÁLIDO");
                            }
                        }		  
                    });
                })
                if(produto1.val().length>0){
                produto1.keyup();
                produto2.keyup();
                }              
            }
            function validar() {
                let descricao1 = $("#produto1").val();
                let descricao2 = $("#produto2").val();
                if (descricao1 == '' || descricao2 == '' || descricao1 == "CÓDIGO INVÁLIDO" || descricao2 == "CÓDIGO INVÁLIDO") {
                    alert("Por favor preencha todos os campos.");
                    return false;
                }else{
                    document.getElementById("contratipo").submit();
                    return true;
                }

            }
        </script>
    </body>
</html>