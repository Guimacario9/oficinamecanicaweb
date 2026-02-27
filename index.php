<?php
session_start();

// CONEXÃO SEPARADA
require_once __DIR__ . "/config/conexao.php";

// DEFINIR ÁREA
$url = isset($_GET['area']) ? $_GET['area'] : 'dashboard';

// PROTEGER
function proteger(){
    if(!isset($_SESSION['usuario_oficina'])){
        header("Location: index.php?area=login");
        exit;
    }
}

// SOMENTE ADMIN
function somenteAdmin(){
    if(!isset($_SESSION['usuario_oficina']) ||
       $_SESSION['usuario_oficina']['nivel'] != 'admin'){
        echo "<h2 class='title'>Permissão Negada</h2>";
        exit;
    }
}

// LOGOUT (APENAS UMA VEZ)
if($url == "logout"){
    session_destroy();
    header("Location: index.php?area=login");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>OficinaSoft - Sistema de Oficina</title>
<link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="main">
<div class="page">

<div class="header">
<div class="header-img">
<h1>OficinaSoft</h1>
<p>Gestão Inteligente para sua Oficina</p>
</div>

<div class="menu">
<ul>
  <li><a href="index.php">Home</a></li>
  <li><a href="?area=servicos">Serviços</a></li>
  <li><a href="?area=sobre">Sobre Nós</a></li>
  <li><a href="?area=contato">Contato</a></li>

<?php if(isset($_SESSION['usuario_oficina'])): ?>

  <li><a href="?area=clientes">Clientes</a></li>
  <li><a href="?area=veiculos">Veículos</a></li>
  <li><a href="?area=os">Ordem de Serviço</a></li>
  
  <?php if($_SESSION['usuario_oficina']['nivel'] == 'admin'): ?>
   <li><a href="?area=relatorio">Relatórios</a></li>
  <?php endif; ?>

  <li><a href="?area=logout">Sair</a></li>

<?php else: ?>
  <li><a href="?area=login">Login</a></li>
<?php endif; ?>

</ul>
</div>
</div>

<div class="content">

<div class="left-panel">
<div class="left-panel-in">

<?php
switch($url){

    // ÁREAS PÚBLICAS
    case 'servicos':
        include __DIR__ . "/servico.php";
    break;

    case 'sobre':
        include __DIR__ . "/sobre.php";
    break;

    case 'contato':
        include __DIR__ . "/contato.php";
    break;

    case 'login':
        include __DIR__ . "/login.php";
    break;

    // ÁREAS PRIVADAS
    case 'clientes':
        proteger();
        include __DIR__ . "/cliente.php";
    break;

    case 'editar_cliente':
        proteger();
        include __DIR__ . "/editar_cliente.php";
    break;

    case 'registrar_usuario':
        Include __DIR__ . "/registrar_usuario.php";
    break;

    case 'veiculos':
        proteger();
        include __DIR__ . "/veiculo.php";
    break;

    case 'os':
        proteger();
        include __DIR__ . "/ordem.php";
    break;

    case 'pagamento':
        proteger();
        include __DIR__ . "/pagamento.php";
    break;

    case 'relatorio':
        proteger();
        somenteAdmin();
        include __DIR__ . "/relatorio.php";
    break;

    default:

        if(isset($_SESSION['usuario_oficina'])){
            echo "<h2 class='title'>Dashboard</h2>";
            echo "<p>Bem-vindo, "
                . htmlspecialchars($_SESSION['usuario_oficina']['nome'])
                . ".</p>";
        } else {
            echo "<h2 class='title'>Bem-vindo ao OficinaSoft</h2>";
            echo "<p>
                    Somos especialistas em oferecer soluções para oficinas.
                    Faça login para acessar o sistema.
                  </p>";
        }

    break;
}
?>

</div>
</div>

<div class="right-panel">
<div class="right-panel-in">

<h3>Status do Sistema</h3>

<?php if(isset($_SESSION['usuario_oficina'])): ?>
<ul>
  <li>✔ Login Ativo</li>
  <li>✔ Banco Conectado</li>
  <li>Usuário: <?php echo htmlspecialchars($_SESSION['usuario_oficina']['nome']); ?></li>
</ul>
<?php else: ?>
<ul>
  <li>Usuário não logado</li>
</ul>
<?php endif; ?>

</div>
</div>

</div>

<div class="footer">
<p>&copy; 2026 OficinaSoft</p>
</div>

</div>
</div>

</body>
</html>