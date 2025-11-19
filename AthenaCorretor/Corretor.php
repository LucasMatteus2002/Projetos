<?php
// =============================
// INICIALIZAÇÃO DE SESSÃO E INCLUDES
// =============================

session_start();
include 'includes/conexao.php';


// Verifica se o usuário está logado, caso contrário redireciona para o login
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    header('Location: tela de login/login.php');
    exit;
}

// Carrega o autoload do Composer
require  'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


// Variáveis usadas no processamento do texto
$mensagem_de_erro = "";
$texto_para_corrigir = "";

// =============================
// PROCESSAMENTO DO TEXTO ENVIADO
// =============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Caso o botão de logout seja pressionado
    if (isset($_POST['acao']) && $_POST['acao'] === 'logout') {
        session_unset();
        session_destroy();
        header('Location: tela de login/login.php');
        exit;
    }

    // Caso o botão de corrigir outro texto seja pressionado
    if (isset($_POST['acao']) && $_POST['acao'] === 'corrigir_novamente') {
        header('Location: Corretor.php');
        exit;
    }

    // Recebe o texto do textarea
    $texto_para_corrigir = trim($_POST['texto_para_corrigir']);

    // Se o campo estiver vazio, exibe erro
    if (empty($texto_para_corrigir)) {
        $mensagem_de_erro = "ERRO: NENHUM TEXTO FOI INSERIDO.";
    } else {
        // =============================
        // INTEGRAÇÃO COM A API DO GEMINI
        // =============================

        $google_api_key = $_ENV ['API_KEY']; // substitua pela sua chave real
        $model = "gemini-2.5-flash-lite";
        $api_url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $google_api_key;

        $prompt = "Você é um corretor de texto, corrija a ortografia e gramática do texto que receber, e devolva o texto corrigido na parte de cima da área de retorno. No entanto, você também é a Deusa Athena, deusa da sabedoria. Quero que use sua sabedoria do Olimpo para ajudar os mortais em sua tarefa de corrigir textos. Explique os erros na parte de baixo. Separe com clareza os campos, como erros gramaticais, texto resumido, etc.Separe os tópicos com títulos em negrito, porém não use asteriscos, a explicação não deve ser longa. Na hora da correção, explique os erros de forma que os mortais entendam e se encantem com sua sabedoria. Se possível, separe bem sua correção em categorias, com emojis criativos que correspondam ao contexto.. Texto:  " . $texto_para_corrigir;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $texto_corrigido = $result['candidates'][0]['content']['parts'][0]['text'];

            // Salva no histórico de correções
            $stmt = $conn->prepare("INSERT INTO histórico_correções (usuario, texto_original, texto_corrigido) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_SESSION['usuario'], $_POST['texto_para_corrigir'], $texto_corrigido);
            $stmt->execute();
            $stmt->close();

            // Atualiza o textarea com o texto corrigido
            $texto_para_corrigir = $texto_corrigido;
        } else {
            $mensagem_de_erro = "Erro ao processar a resposta da API.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almendra:ital,wght@0,400;0,700;1,400;1,700&family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="icone.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS externo -->
    <title>Corretor de texto (Athena)</title>
</head>
<body>
    <!-- ============================= -->
    <!-- CONTAINER DE APRESENTAÇÃO -->
    <!-- ============================= -->
    <div id="container-a">
        <h1>Athena</h1>
        <p>Bem-vindos, mortais! Vou emprestar um pouco da minha sabedoria!</p>
    </div>

    <div id="intro-section">
        <h2>Chamo-me Athena. Fui convocada para ajudá-los, pois fui desenvolvida para a correção de textos.</h2>
    </div>

    <!-- ============================= -->
    <!-- FORMULÁRIO PRINCIPAL -->
    <!-- ============================= -->
    <form method="POST" action="Corretor.php">
        <div>
            <!-- Atributo 'name' é ESSENCIAL para o PHP receber o texto -->
            <textarea name="texto_para_corrigir" placeholder="Insira seu texto e seja abençoado com o saber do Olimpo..." class="textarea"><?php echo htmlspecialchars($texto_para_corrigir); ?></textarea>
            <br>
        </div>

        <div class="botões">
            <!-- Botão para enviar o texto à API -->
            <button type="submit" class="botão">Aperte para corrigir</button>

            <!-- Botão para recarregar e corrigir novo texto -->
            <button type="submit" name="acao" value="corrigir_novamente" class="corrigir">Corrigir outro texto</button>

            <!-- Botão de logout -->
            <button type="submit" name="acao" value="logout" class="voltar">Voltar</button>
        </div>
    </form>

    <!-- ============================= -->
    <!-- EXIBIÇÃO DO HISTÓRICO E ERROS -->
    <!-- ============================= -->
    <div style="text-align:center; margin-top:30px; color:white;">
        <?php
        // Mostra mensagens de erro, se existirem
        if (!empty($mensagem_de_erro)) {
            echo '<p>' . htmlspecialchars($mensagem_de_erro) . '</p>';
        }

        // Mostra o histórico de correções anteriores
        $usuario_logado = $_SESSION['usuario'];
        $resultado = $conn->query("SELECT * FROM histórico_correções WHERE usuario = '$usuario_logado' ORDER BY data_hora DESC");

        if ($resultado && $resultado->num_rows > 0) {
            echo '<h2>Histórico de Correções</h2>';
            echo '<ul style="list-style:none;">';
            while ($linha = $resultado->fetch_assoc()) {
                echo '<li><strong>' . htmlspecialchars($linha['data_hora']) . '</strong>: ' . htmlspecialchars(substr($linha['texto_original'], 0, 80)) . '...</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Nenhum histórico encontrado.</p>';
        }
        ?>
    </div>
</body>
</html>
