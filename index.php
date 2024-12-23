<?php
// Caminho para o arquivo XML
$xmlFilePath = 'C:\Users\Teste\Desktop\teste\registro_logons.xml'; // Ajuste o caminho conforme necessário

// Verifica se o arquivo XML existe
if (!file_exists($xmlFilePath)) {
    die("Erro: O arquivo XML não foi encontrado.");
}

// Carrega o conteúdo do arquivo XML
$xmlData = simplexml_load_file($xmlFilePath);

// Verifica se houve erro na carga do XML
if ($xmlData === false) {
    die("Erro ao carregar o arquivo XML.");
}

// Variáveis de pesquisa
$searchMatricula = $_GET['matricula'] ?? ''; // Busca por matrícula
$searchComputador = $_GET['computador'] ?? ''; // Busca por computador
$searchData = $_GET['data'] ?? ''; // Busca por data
$searchHora = $_GET['hora'] ?? ''; // Busca por hora

// Filtra os logs com base nos critérios de pesquisa
$filteredLogs = [];
foreach ($xmlData->Registro as $log) {
    $matricula = (string) $log->Matricula;
    $computador = (string) $log->Computador;
    $data = (string) $log->Data;
    $hora = (string) $log->Hora;

    if (
        (!$searchMatricula || stripos($matricula, $searchMatricula) !== false) &&
        (!$searchComputador || stripos($computador, $searchComputador) !== false) &&
        (!$searchData || stripos($data, $searchData) !== false) &&
        (!$searchHora || stripos($hora, $searchHora) !== false)
    ) {
        $filteredLogs[] = [
            'Matricula' => $matricula,
            'Computador' => $computador,
            'Data' => $data,
            'Hora' => $hora,
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa de Logins</title>
    <!-- Incluindo Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 900px;
            margin-top: 30px;
        }

        .sticky-top {
            top: 0;
            z-index: 1020;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .no-results {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegação Fixa no Topo -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistema de Pesquisa de Logins</a>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Pesquisa de Logins</h4>
            </div>
            <div class="card-body">
                <form method="GET">
                    <div class="mb-3">
                        <label for="matricula" class="form-label">Matrícula:</label>
                        <input type="text" class="form-control" id="matricula" name="matricula" value="<?= htmlspecialchars($searchMatricula) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="computador" class="form-label">Computador:</label>
                        <input type="text" class="form-control" id="computador" name="computador" value="<?= htmlspecialchars($searchComputador) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="data" class="form-label">Data:</label>
                        <input type="text" class="form-control" id="data" name="data" value="<?= htmlspecialchars($searchData) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="hora" class="form-label">Hora:</label>
                        <input type="text" class="form-control" id="hora" name="hora" value="<?= htmlspecialchars($searchHora) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </form>
            </div>
        </div>

        <h2 class="mt-4">Resultados da Pesquisa</h2>
        <?php if (count($filteredLogs) > 0): ?>
            <table class="table table-striped table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Matrícula</th>
                        <th>Computador</th>
                        <th>Data</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredLogs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['Matricula']) ?></td>
                            <td><?= htmlspecialchars($log['Computador']) ?></td>
                            <td><?= htmlspecialchars($log['Data']) ?></td>
                            <td><?= htmlspecialchars($log['Hora']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">Nenhum registro encontrado.</p>
        <?php endif; ?>
    </div>

    <!-- Incluindo o JavaScript do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
