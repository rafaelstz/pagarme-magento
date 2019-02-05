# Magento Extension for [Pagar.me](https://pagar.me) Payment Gateway (Magento CE 1.7+)

## Instalação

### Instalar usando o [modgit](https://github.com/jreinke/modgit):

    $ cd /path/to/magento
    $ modgit init
    $ modgit add pagarme git@github.com:pagarme/pagarme-magento.git

### Instalar usando o [modman](https://github.com/colinmollenhour/modman):

    $ cd /path/to/magento
    $ modman init
    $ modman clone git@github.com:pagarme/pagarme-magento.git

### ou baixar e instalar manualmente:

* Baixe a ultima versão [aqui](https://github.com/pagarme/pagarme-magento/archive/master.zip)
* Descompacte o arquivo baixado e copie as pastas ``` app```, ```js``` e ```skin``` para dentro do diretório principal do Magento
* Limpe a cache em ```Sistema > Gerenciamento de Cache```


>Copiando as pastas app, js, lib e skin
>Assim como o segundo item diz, você precisa copiar algumas pastas para o diretório principal do Magento, que já é composto pelas pastas app, js e skin. Ou seja, o que você precisa é mesclar o conteúdo que acabou de baixar do repositório Pagar.me com o que já existe no Magento.


O próximo passo é configurar o módulo Pagar.me para que consiga criar as transações. Para fazer isso, siga o seguinte caminho no seu painel Magento: ```Sistema > Configuração > Vendas > Métodos de Pagamento``` — ou, caso seu Magento esteja em Inglês: ```System > Configuration > Sales > Payment Methods```

Feito isso, você deve ter acesso à seguinte tela:

![Passo 1](https://i.imgur.com/osCuCDj.png)

#### Vamos a uma explicação rápida de cada item:
##### Pagar.me - Configuração:
Este item diz respeito à configuração das chaves de produção e teste para o seu ambiente, definidas na sua Dashboard Pagar.me como Chave de API (API Key) e Chave de Encriptação (Encryption Key).

##### Pagar.me - Boleto:
Se você estiver usando os serviços de Boleto bancário do Pagar.me, este item ajuda a configurar algumas propriedades, como dias para vencimento, instruções, etc.

##### Pagar.me - Cartão de crédito:
Caso você esteja utilizando a opção de Checkout Transparente do Pagar.me para transações com cartão de crédito, aqui você pode configurar taxas de juros, valor mínimo de parcela, etc.

##### Pagar.me - Checkout Pagar.Me:
Este item define também as configurações do Checkout para cartão de crédito, mas utiliza o modal de pagamento Pagar.me para receber as informações do usuário. Veja mais em Checkout


>Campos obrigatórios para criar uma transação:
>Caso você opte pelo Checkout Transparente, é necessário que a sua aplicação garanta o envio dos campos CPF/CNPJ e os campos que compõem os dados dados de endereço (Rua, Número da rua, Bairro e CEP). Lembre-se sempre desses campos, pois a falta de qualquer um deles invalida a criação da transação.


## Configuração

* Configure o modulo em ```Sistema > Configuração > Métodos de Pagamento > Pagar.me - Configuração``` e informe a ```Chave de API``` e a ```Chave de criptografia```, obtidos a partir da sua conta no [Pagar.me](https://pagar.me)

![Passo 2](https://i.imgur.com/4N51yvL.png)

#### Explicando os campos da imagem acima:
##### Modo:
Aqui você define qual é o ambiente que você está usando, entre Teste ou Produção. Faça isso para que os campos a seguir tenham efeito somente neste ambiente específico. Isto é, se você quer fazer apenas um teste, é importante selecionar o modo corretamente para não fazer alterações com impacto real na sua aplicação.

##### Chave de API:
Neste campo você coloca a sua API Key, que é uma chave utilizada para autenticar o seu negócio junto à API do Pagar.me.


>Chave de teste e de produção
>Preste atenção sempre em qual chave você está usando, se é a de teste ou a de produção.
>
>Exemplo de API Key de teste: ak_test_grXijQ4GicOa2BLGZrDRTR5qNQxJW0
>Exemplo de API Key de produção: ak_live_qlSlEXJgsqaCjKohh5AZfnqv7OLP5Q .


##### Chave de criptografia:
Neste campo você deve colocar a sua Encryption Key, que é uma chave essencial para realizar transações com Cartão de crédito usando a API Pagar.me.


>Chave de teste e de produção
>Da mesma forma que o campo anterior, preste atenção sempre em qual chave você está usando, se é a de teste ou a de produção.
>
>Exemplo de Encryption Key de teste: ek_test_aHGru3d5a7qweiftqXg1bQvbN1c7K0
>Exemplo de Encryption Key de produção: ek_live_Wa8CDUuLlFvSQzjxf4YFZIBQvb2l1p


##### Custom Cpf Field e Custom Cnpj Field:
O módulo Pagar.me usa por padrão o campo ```taxvat``` para receber o número de documento de uma pessoa em sua loja. No entanto, caso você tenha implementado algo específico, é preciso informar ao módulo qual é o nome que foi dado para esse campo. Lembrando que essa informação é obrigatória para a criação de uma transação no sistema do Pagar.me.

Agora que você já sabe como preencher cada campo, a sua tela deve estar parecida com essa. Esse exemplo mostra os campos preenchidos para o ambiente de produção:

![Passo 3](https://i.imgur.com/92rgDGe.png)

Depois de preencher tudo, clique em Save Config e vamos ao próximo passo!

### Pagar.me - Boleto
* Para configurar o Boleto, vá em ```Sistema > Configuração > Métodos de Pagamento > Pagar.me - Boleto```

![Passo 4](https://i.imgur.com/vza5dwb.png)

#### Explicando os campos da imagem acima:
##### Ativado:
Indique neste campo se você deseja habilitar ou não o uso do Boleto bancário do Pagar.me na sua aplicação.

##### Título:
Neste campo é necessário indicar o nome que será mostrado para a opção de Boleto bancário na página de finalização de compra no seu site.

##### Status do novo pedido:
Este campo traz o status atribuído ao pedido assim que a compra tenha sido finalizada. Por padrão, o Magento cadastra o primeiro status como ```Pending```. No entanto, você pode gerenciar isso em: ```System > Order Statuses```.

##### Instruções:
Aqui você consegue configurar quais instruções serão passadas no boleto para o caixa que irá processar o pagamento. Por exemplo: ```Não aceitar o pagamento após vencimento```.

##### Dias para vencimento:
Configure neste campo em até quantos dias o boleto gerado vai vencer. Isto é, quantos dias úteis o seu cliente tem para fazer o pagamento pela sua compra.

##### Payment Applicable Form:
Permite definir em quais países o boleto será aceito.

##### Ordem de classificação:
Define a ordem na qual a opção de boleto aparece na lista de meios de pagamento em sua página de finalização de pedido.

##### Send status change notification:
Seguindo as orientações acima, o seu módulo deve estar da seguinte forma:

![Passo 5](https://i.imgur.com/A3tOw0z.png)

Excelente! Vamos ao próximo passo para configurar cobranças por cartão de crédito.

### Pagar.me - Cartão de crédito
As configurações para usar cartão de crédito de forma transparente em sua página de finalização de compra são as seguintes:

![Passo 6](https://i.imgur.com/dNyCN8Q.png)

* Para configurar o Cartão de Crédito, vá em ```Sistema > Configuração > Métodos de Pagamento > Pagar.me - Cartão de Crédito```

#### Explicando os campos da imagem acima:
Ativado:
Indique neste campo se você deseja habilitar ou não a opção de pagamento por Cartão de crédito na sua aplicação.

##### Título:
Neste campo é necessário indicar o nome que será mostrado para a opção de Cartão de crédito na página de finalização de compra no seu site.

##### Status do pedido após confirmação de pagamento na Pagar.me:
Configure neste campo o status da transação assim que o Pagar.me confirmar a captura do valor junto ao banco emissor. Você pode gerenciar novos status em: ```System > Order Statuses```, mas por padrão esse campo é configurado como ```Processando```.

##### Status de Novo pedido:
Configure aqui o status intermediário **entre** a confirmação de captura junto ao banco e a criação do pedido na sua plataforma. Por padrão você pode usar ```Pending```, mas é possível gerenciar novos status em: ```System > Order Statuses```.

##### Ação de Pagamento:
Os valores possíveis desse campo são:

* **Autorizar**: com essa opção ativada, acontece apenas a autorização do pagamento. Depois, é preciso fazer a captura manualmente via Dashboard. Este processo é indicado apenas para casos em que, por alguma razão, você tenha que analisar o pedido, ver se existe estoque etc.

* **Autorizar e capturar**: quando essa opção é selecionada, o módulo Magento informa à API Pagar.me para fazer o fluxo de informação completo. Isto é, é feita a reserva e a confirmação junto ao banco emissor, para que seja feita a cobrança da quantia passada no cartão utilizado.


>Diferença entre autorização e captura
>Para entender melhor qual é a diferença entre autorização e captura, veja: Autorização e captura. Dessa forma, você pode escolher qual modelo serve melhor à sua realidade.


##### Async:
Esta propriedade, quando selecionada como **true**, faz com que o processo de criação de transação seja assíncrono. Ou seja, a sua loja recebe como primeira resposta da API um status intermediário chamado ```processing``` e, uma vez que a transação já tenha um status definido (```paid```, ```refused``` etc), o seu servidor recebe uma notificação da API para que o pedido seja atualizado de acordo com o status final. A opção **Async** é particulamente importante se a sua loja recebe um número muito alto de requisições e precisa responder rapidamente aos clientes.

##### Tipos de Cartão de Crédito:
Este campo define quais bandeiras de cartão você vai aceitar em sua loja.

##### Número máximo de parcelas:
Este campo define em até quantas vezes será possível parcelar um pedido. Deve ser um valor de um pedido. Deve ser um valor de um a doze.

##### Valor mínimo por parcela:
Este campo define qual o valor mínimo para as parcelas de um pedido.

##### Juros(a.m):
O módulo Pagar.me oferece a conveniência de calcular (via juros simples) o valor das parcelas de acordo com um valor de juros passado neste campo. Configure este valor usando ponto, e **não** vírgula. Caso a sua aplicação não cobre juros, é preciso deixar o campo como 0.

##### Número de parcelas sem juros:
Esta opção define em até quantas parcelas o pedido não terá juros. Vale ressaltar que, quando o cliente final seleciona uma quantia de parcelas maior do que a definida neste campo, todas as parcelas terão juros aplicados.

##### Invoice Email:
Payment Applicable Form:
Permite definir em quais países essa opção será aceita.

##### Ordem de classificação:
Define a ordem em que a opção de cartão de crédito irá aparecer na lista de meios de pagamento na sua página de finalização de pedido. Esse valor precisa ser diferente do que foi configurado para o mesmo campo na tela de Boleto bancário.

Excelente! Com as instruções acima você já consegue habilitar cartão de crédito como método de pagamento em seu site. Veja a seguir um exemplo de como essa tela de configuração deve ficar depois de preenchida:

![Passo 7](https://i.imgur.com/JgN0Ppu.png)

### Pagar.Me - Checkout

* Caso o checkout que você utilize tenha um evento personalizado no on-change do botão finalizar compra diferente de "payment", vá em ```Sistema > Configuração > Métodos de Pagamento > Pagar.me - Checkout``` e adicione no campo "Checkout payment onclick save" o value do on-change personalizado do seu checkout.

O modal de pagamento Checkout Pagar.me é um dos produtos do Pagar.me, que facilita a inclusão de informações sobre cobrança do pedido. Usar esse recurso tem um efeito positivo direto na sua taxa de conversão.

Uma vez configurado, ele habilita a seguinte opção na sua página de finalização de pedido:

![Passo 8](https://i.imgur.com/kOFWYP8.png)

Dessa forma, quando o seu cliente clicar em "Preencher os dados do cartão", a próxima tela será exibida, como essa:

![Passo 9](https://i.imgur.com/HKgRZgR.png)

Quer saber como configurar o Checkout do Pagar.me? Vamos lá:

![Passo 10](https://i.imgur.com/KVDFTcW.png)

##### Valor de Desconto no Boleto:
Este campo define um valor fixo de desconto para um Boleto bancário.

##### Porcentagem de Desconto no Boleto:
Este campo define a porcentagem de desconto aplicada ao valor total do pedido, para Boletos bancários.

##### Checkout payment onclick save:
Este é um campo ```True/False```, que deve ser configurado da seguinte forma:

* Quando o valor desse campo for ```true```, automaticamente o pedido será enviado para o fluxo de informação junto à API Pagar.me.
* Quando esse valor for ```false```, o cliente terá que manualmente clicar em **Finalizar pedido**.

### Configuração de campos de Cliente

Após realizar a configuração do módulo do Pagar.me, é necessário realizar algumas modificações em relação as propriedades do seu **cliente** antes de começar a transacionar. Para isso, é necessário seguir o seguinte caminho: ```Sistema > Configuração``` (ou, caso o seu Magento esteja em Inglês, ```System > Configuration```).

![Passo 11](https://i.imgur.com/XZ0Q82M.png)

Na página de configuração é necessário localizar no menu lateral esquerdo a aba "Cliente" e clicar em ```configuração de clientes``` (ou, se seu Magento estiver em inglês, ```customer configuration```) e uma nova página será exibida, como a que é mostrada abaixo:

![Passo 12](https://i.imgur.com/ZLk21EU.png)

Nesta nova página, clique na aba ```Opções de nomes e endereços``` (ou, se seu Magento estiver em inglês, ```Name and address options```) e configure os campos da maneira que estão na imagem abaixo.

![Passo 13](https://i.imgur.com/HV0dxia.png)

#### Veja o que significa cada campo:
##### Numero de linhas no campo de endereço:
Diz respeito a quantidade de campos que o seu cliente precisa cadastrar para realizar a compra em seu site. Como o módulo do Pagar.me obriga que o endereço tenha 4 campos, é necessário alterar este campo para 4.
Os quatro campos são, respectivamente, street (nome da rua), street_number (número da casa ou prédio), complementary (complemento) e neighborhood (bairro).

##### Mostrar o número Tax/VAT:
Este campo precisa ser marcado como required, pois ele representará o número de CPF ou CNPJ do seu cliente. Como este parâmetro é obrigado pelo nosso módulo, uma transação não é criada sem ele.


## Para desenvolvedores - Avançado

### Requisitos

- [Docker](https://docs.docker.com)
- [Docker Compose](https://docs.docker.com/compose/)

### Instalando o Magento Community 1.x

1. Execute o comando `docker-compose up -d` para iniciar os containers e a instalação do Magento
2. Execute o comando `docker-compose logs -f magento` para acompanhar o processo de instalação.

### Acessando a loja virtual através do navegador

1. Altere seu arquivo `/etc/hosts` adicionando a entrada `127.0.0.1 magento`
2. Acesse a loja no navegador utilizando o endereço `http://magento`

### Acessando a área administrativa

1. Acesse `http://magento/admin
2. Utilize `admin` para o usuário e `magentorocks1` para a senha

### Tests
wget https://phar.phpunit.de/phpunit-4.1.0.phar
chmod +x phpunit-4.1.0.phar
mv phpunit-4.1.0.phar /usr/bin/phpunit
