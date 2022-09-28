<?php

namespace CP;

final class CrawlerParana
{
  private $url = "http://cnpj.info/Empresa-Auxiliar-de-Servicos-Gerais-do-Parana-Ltda-Empresa-Auxiliar-de-Servicos-Gerais-do-Parana";
  private $depth = 5;

  public function fazBusca($letras, $cnpj)
  {
    // Pega os valores dos campos que foram enviados pelo formulário
    // *sem validação mesmo, é só pra exemplo tá?
    #Coisas importantes para dizer ao $ch logo mais

    //IMPORTANTE que o caminho esteja correto e tenha permissão CHMOD 777
    $cookie = 'receita.txt';

    // não sei.. coloquei pra garantir
    $reffer = "http://google.com";

    //sempre é bom ter para garantir a entrada do seu serviço
    $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.4) Gecko/20030624 Netscape/7.1 (ax)";

    //url da receita que valida o formulário
    $url = "http://www.sintegra.fazenda.pr.gov.br/sintegra/";

    //dados do POST do formulário da receita. 
    //** Muito importante entender os formulários que você esteja trabalhando **
    //os campos NESTA EXATA ordem funcionaram legal ;)

    $post_fields = "data[Sintegra1][Cnpj]=$cnpj&data[Sintegra1][CodImage]={$letras}&empresa=Consultar%20Empresa";

    //agora sim.. 1, 2, 3 VALENDO! 
    $ch = curl_init();

    curl_setopt_array($ch, array(
      CURLOPT_URL => $url, //sem isso, seu cURL é imprestável
      CURLOPT_POST => 1, //afirmo que ele irá fazer um POST
      CURLOPT_POSTFIELDS => $post_fields, //quais são os campos que estarei enviando ao valida.asp?
      CURLOPT_USERAGENT => $agent, //ahh é importante sempre ter né =D
      CURLOPT_REFERER => $reffer, //não sei.. coloquei pra garantir
      CURLOPT_COOKIEFILE => $cookie, //lembra dos cookies que guardamos qndo digitamos o captcha? 
      CURLOPT_COOKIEJAR => $cookie,  //então, precisamos deles :)
      CURLOPT_FOLLOWLOCATION => 1, // não quero explicar, mas é importante. pesquisa ae depois ;)
      CURLOPT_RETURNTRANSFER => 1, // quer ver os dados? então sempre ative esta opção no seu script
      CURLOPT_HEADER => 0, // sem header
    ));

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    if (strpos($info['url'], 'error') != false) {
      $response = ['erro' => $this->formataRespostaError($result)];
    } else {
      $response = $this->formataResposta($result);
    }
    var_dump($response);
    return $response;
  }



  public function recebe_imagem($url, $arquivo)
  {
    $cookie = 'receita.txt'; //Importantissimo que o caminho esteja correto e com permissão CHMOD 777

    $ch = curl_init();

    curl_setopt_array($ch, array(
      CURLOPT_URL => $url, //url que produz a imagem do captcha.
      CURLOPT_COOKIEFILE => $cookie, //esse mais o debaixo fazem a mágica do captcha
      CURLOPT_COOKIEJAR => $cookie,  //esse mais o de cima fazem a mágica do.. ah já falei isso;
      CURLOPT_FOLLOWLOCATION => 1, //não sei, mas funciona :D
      CURLOPT_RETURNTRANSFER => 1, //retorna o conteúdo.
      CURLOPT_BINARYTRANSFER => 1, //essa tranferencia é binária.
      CURLOPT_HEADER => 0, //não imprime o header.
    ));

    $data = curl_exec($ch);

    curl_close($ch);

    //salva a imagem
    $fp = fopen($arquivo, 'w');
    fwrite($fp, $data);
    fclose($fp);

    //retorna a imagem
    return $arquivo;
  }

  public function formataResposta($pagina)
  {
    // $arquivo = 'consultaCNPJVALIDO.html';
    // $fp = fopen($arquivo, 'r+');
    // // Lê o arquivo (se existir) 
    // $ler = fread($fp, filesize($arquivo));
    // fclose($fp);
    // $td = [];
    $pagina =  utf8_encode($pagina);
    preg_match_all("/\<td(.*)>(((\w|[(]|\n){1}).*)<\/td>/m", $pagina, $td);
    return $td[2][6];
  }

  public function formataRespostaError($pagina)
  {
    // $arquivo = 'consultaCNPJINVALIDO.html';
    // $fp = fopen($arquivo, 'r+');
    // // Lê o arquivo (se existir) 
    // $ler = fread($fp, filesize($arquivo));
    // fclose($fp);
    // $td = [];
    $pagina =  utf8_encode($pagina);
    preg_match_all("/\<td class=\"erro_msg_custom\"(.*)>(((\w|[(]|\n){1}).*)<\/td>/m", $pagina, $td);
    return $td[2];
  }
}
