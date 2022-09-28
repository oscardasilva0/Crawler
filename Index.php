<?php
include 'CrawlerParana.php';

use  CP\CrawlerParana as CP;
//gera imagem
(new CP())->recebe_imagem("http://www.sintegra.fazenda.pr.gov.br/sintegra/captcha?0.871021947306771", "receita.gif");
$cnpj = "";
$letras = readline("Digite as letras:");
(new CP())->fazBusca($letras, $cnpj);
//clear(new CP())->formataResposta();
