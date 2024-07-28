<?php
// Desative o cache para que a imagem seja buscada sempre
$timestamp = gmdate("D, d M Y H:i:s") . " GMT-3";
header("Expires: $timestamp");
header("Last-Modified: $timestamp");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

// Seta como Imagem SVG
header("Content-type: image/svg+xml");

// incrementa o arquivo e retorna o contador atual
function incrementFile($filename): int
{
    if (file_exists($filename)) {
        // abre o arquivo
        $fp = fopen($filename, "r+") or die("Failed to open the file.");
        // bloqueia o arquivo
        flock($fp, LOCK_EX);
        // lê o conteúdo do arquivo e incrementa 1
        $count = (int) fread($fp, filesize($filename)) + 1;
        // trunca o arquivo
        ftruncate($fp, 0);
        // volta para o início do arquivo
        fseek($fp, 0);
        // escreve o novo valor no arquivo
        fwrite($fp, $count);
        // libera o bloqueio
        flock($fp, LOCK_UN);
        // fecha o arquivo
        fclose($fp);
    }
    // cria se não existir
    else {
        // cria o arquivo com o valor 1
        $count = 1;
        file_put_contents($filename, $count);
    }
    // retorna o contador atual
    return $count;
}

// formata um número para uma versão mais curta
function shortNumber($num)
{
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}

// função para obter o conteúdo de uma URL
function curl_get_contents($url): string
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// incrementa o contador de visualizações
$message = incrementFile("views.txt");

// parâmetros para o SVG
$params = [
    "label" => "Views",
    "logo" => "github",
    "message" => shortNumber($message),
    "color" => "purple",
    "style" => "for-the-badge"
];

// URL da imagem
$url = "https://img.shields.io/static/v1?" . http_build_query($params);

// exibe a imagem
echo curl_get_contents($url);
?>
