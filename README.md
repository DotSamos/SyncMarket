<p align="center">
  <img src="https://i.imgur.com/bKcDUpC.png" width="700px" alt="Logo SyncMarket">
</p>
<p align="center">
  :mailbox: Um plugin de ativação automática de compras feito para <a
    href="https://www.syncmarket.com.br">SyncMarket</a>.
</p>

</br>

<h1>:pushpin: Como funciona?</h1>
<p>
  O plugin foi faz requisições para a api de compras do site e procura pelas compras feitas na última hora, desta forma
  o sistema filtra as compras que já tiveram seu pagamento aprovado e faz uma validação seguindo a regra de negócio para
  decidir se aquele produto pode ser ativado ou não, caso ele seja ativado vai executar o comando configurado no site
  para ativação do produto e uma vez isto realizado vai notificar o site que o produto foi ativado, assim atualizando o
  status da compra para entregue.
</p>

</br>

<h1>:gear: Compatibilidade</h1>
<p>
  Este plugin foi feito para servidores <a href="https://pmmp.io">PocketMine-MP</a>, no entanto caso seja da comunidade java que cria servidores utilizando Bukkit, Spigot, Paper e mais uma infinidade de modificações do Bukkit temos <a href="#">um plugin</a> feito também para os servidores java.
</p>


<h1>:inbox_tray: Atualizações?</h1>
<p>Relaxa, <strong>correções de bugs</strong>, <strong>erros</strong> ou até mesmo <strong>suporte as versões
    futuras</strong> vão acontecer sempre que necessárias.</p>
<p><strong>LEMBRANDO:</strong> Se tive problemas com o plugin (erros, bugs ou até mesmo dúvidas) entre em <a
    href="https://syncmarket.com.br/">contato</a> ou abra uma <a
    href="https://github.com/SamosMC/SyncMarket/issues">issue</a>.</p>

</br>

<h1>:hammer: Por onde posso começar?</h1>
<p>1. Crie uma conta em <a href="https://app.syncmarket.com.br/register">nosso site</a> e logo após isso já pode começar
  criando sua loja utilizando até mesmo um plano <strong>free</strong>, no entanto este plano <strong>não</strong> dá
  suporte a ativação automática:</p>
<p align="center">
  <img src="https://imgur.com/uaInGti.png" width="300px" height="500px" alt="Painel de registro">
  <img src="https://imgur.com/DC91095.png" width="300px" height="500px" alt="Plano free">
</p>

<p>2. Logo após isso já pode criar um servidor para ter acesso ao <strong>token</strong> deste (quando for criar um
  produto vai ser pedido para selecionar em qual servidor ele vai ser ativado, nisso este token gerado deve ficar na
  configuração do plugin deste servidor):</p>
<p align="center">
  <img src="https://i.imgur.com/RQC4hLm.png" alt="Campo dos servidores">
  <img src="https://imgur.com/0lPkFWT.png" alt="Criar servidor">
  <img src="https://imgur.com/l2K0Fot.png" alt="Campo do token">
</p>

<p>Como já deve ter reparado na imagem acima ali já é possível ver o token respectivo do servidor que criou, então basta
  o copiar e agora vamos para a configuração 'config.yml' que é gerada na primeira vez que o plugin é ligado em seu
  servidor:</p>
<p align="center"><img src="https://imgur.com/0jtXJFR.png" alt="Configurando o token"></p>
<p>Basta colocar seu token no campo "server-token" e pronto, já está tudo funcionando. Quando ligar seu servidor já deve
  encontrar esta mensagem:</p>
<p align="center"><img src="https://imgur.com/8tN5BfU.png" alt="Confirmação da validação do token"></p>
<p>Se por ventura se encontrar com uma mensagem de erro verifique se copiou o token corretamente, mas caso o erro
  persistir entre em contato com nossa equipe de suporte.</p>

<p>3. E agora é só criar uma loja (caso não tenha) e nela já criar um produto e durante sua criação definir que ele deve
  ser ativado no servidor que havia criado anteriormente.</br>Se tiver feito tudo certo após o pagamento do produto ser aprovado ele vai ser ativado em no máximo 10 minutos se estiver utilizando a configuração padrão do plugin.
</br>

<h1>:bulb: Funcionalidades extras?</h1>
<p>
  Sim, temos algumas funcionalidades que talvez venham a ser úteis a você.</br>
  Vamos começar pela nossa configuração, não vou falar muito dela e do que existe para configurar afinal está
  literalmente tudo explicado lá. Agora confira algumas funcionalidades que venham a merecer sua atenção:
</p>

<h2>:gift: Um bônus para todos</h2>

</br>
<p align="center"><img src="https://imgur.com/L7lIOmy.png" alt="Configurações do sistema de bônus"></p>
</br>

<p>
  É um sisteminha bem simples, porém pode incentivar seus jogadores a comprarem mais itens em sua loja, e ainda além de
  ser totalmente configurável o bônus é "enviado" executando um comando no terminal, o que deixa isso ser ainda mais
  flexível.</br>
  MAS, obviamente isso não é nada impressionante, por isso conto com a colaboração de vocês para dar um feedback no
  projeto seja assinando o plano pago ou apenas sugerindo uma alteração ou adição :smiley:.
</p>

<h2>Acompanhe a ativação</h2>
<p align="center"><img src="https://imgur.com/w5PQVJ6.png" alt="Log da ativação de produtos"></p>

<p>
  Além de termos uma log para os desenvolvedores acompanharem e verem qualquer erro que o sistema possa ter nas requisições temos uma outra log <strong>especialmente feita</strong> para registras a <strong>ativação dos produtos</strong>, desta forma você sempre vai ter o controle sobre quando e para quem um determinado produto foi ativado. E não pense que essas logs vão ficar <strong>bagunçadas</strong>, cada arquivo de log contém apenas as ocorrências daquele <strong>determinado dia</strong>, então produtos ativados no dia "x" estão no arquivo "x.log" e os produtos ativados no dia "y" no arquivo "y.log".
</p>

</br>

<h1>:octocat: Interagindo com o sistema</h1>
<p>
  Os plugins do seu servidor podem interagir com grande parte do sistema da SyncMarket. Deixamos as informações como compras pendentes do jogador em cache, o qual atualizamos conforme o tempo configurado.</br>
  Caso seja desenvolvedor ou apenas curioso para querer brincar com as informações disponíveis no plugin consulte a
  <a href="https://github.com/SamosMC/SyncMarket/wiki">wiki/documentação</a>.
</p>

</br>

<h1>:link: Links</h1>
<ul>
  <li><a href="https://syncmarket.com.br/">Site/Loja</a></li>
  <li><a href="https://discord.syncmarket.com.br/">Servidor do Discord</a></li>
  <li><a href="https://www.instagram.com/syncmarket.com.br/">Instagram</a></li>
</ul>

</br>

<p align="center"><strong>© 2021 SyncMarket ltda.</strong></p>
<p align="center"><a href="https://twiter.com/SamosMC">@SamosMC</a></p>