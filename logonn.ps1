# Diretório onde os registros serão salvos (compartilhamento de rede)
$diretorioDestino = "C:\Users\Teste\Desktop\teste"

# Verifica se o diretório existe, se não, tenta criá-lo
if (!(Test-Path -Path $diretorioDestino)) {
    try {
        New-Item -ItemType Directory -Path $diretorioDestino -Force | Out-Null
    } catch {
        Write-Host "Erro: Não foi possível acessar ou criar o diretório $diretorioDestino." -ForegroundColor Red
        exit
    }
}

# Caminho do arquivo XML
$arquivoXml = Join-Path $diretorioDestino "registro_logons.xml"

# Captura as informações necessárias
$matricula = $env:USERNAME           # Nome do usuário (matrícula)
$computador = $env:COMPUTERNAME      # Nome do computador
$dataAtual = (Get-Date).ToString("yyyy-MM-dd") # Apenas a data
$horaAtual = (Get-Date).ToString("HH:mm:ss")   # Apenas a hora

# Dados do log atual (em formato de objeto customizado)
$registroAtual = [PSCustomObject]@{
    Matricula = $matricula
    Computador = $computador
    Data = $dataAtual
    Hora = $horaAtual
}

# Verifica se o arquivo XML existe
if (Test-Path -Path $arquivoXml) {
    try {
        # Carrega os registros existentes do arquivo XML
        $registrosExistentes = [xml](Get-Content -Path $arquivoXml)
    } catch {
        # Caso o arquivo esteja corrompido, recria a estrutura
        Write-Host "Aviso: Arquivo XML corrompido. Criando um novo." -ForegroundColor Yellow
        $registrosExistentes = New-Object -TypeName System.Xml.XmlDocument
        $raiz = $registrosExistentes.CreateElement("Registros")
        $registrosExistentes.AppendChild($raiz) | Out-Null
    }
} else {
    # Cria a estrutura inicial do arquivo XML se não existir
    $registrosExistentes = New-Object -TypeName System.Xml.XmlDocument
    $raiz = $registrosExistentes.CreateElement("Registros")
    $registrosExistentes.AppendChild($raiz) | Out-Null
}

# Adiciona o novo registro
$nodoRegistro = $registrosExistentes.CreateElement("Registro")

# Adiciona os campos no registro
foreach ($chave in $registroAtual.PSObject.Properties) {
    $nodoCampo = $registrosExistentes.CreateElement($chave.Name)
    $nodoCampo.InnerText = $chave.Value
    $nodoRegistro.AppendChild($nodoCampo) | Out-Null
}

# Adiciona o registro à raiz
$registrosExistentes.DocumentElement.AppendChild($nodoRegistro) | Out-Null

# Salva o arquivo XML
$registrosExistentes.Save($arquivoXml)

# Mensagem de confirmação (opcional)
Write-Host "Log registrado com sucesso: $($registroAtual | ConvertTo-Json -Depth 10)" -ForegroundColor Green
