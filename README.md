# ReclameAqui para CSV
Salva reclamções do ReclameAqui em um arquivo CSV 

## Depende

- Docker
- docker-compose

## Como Instalar

Baixe o projeto

```
git clone https://github.com/jacsonsantos/reclame-aqui-to-csv.git
```

Acesse o projeto
```
cd reclame-aqui-to-csv
```

Build a imagem e levante o container
```
docker-compose up -d --build
```

## Como extrair as reclamações

Acesse o container
```
docker-compose exec reclame_crawler bash
```

Rodar o script passando o URL da empresa
```
php vendor/bin/reclameaqui.php https://www.reclameaqui.com.br/empresa/ligga-telecom/
```

As reclamações estarão salvas no diretório `dataset` com o nome da empresa.
`./dataset/ligga-telecom.csv`

## Exemplo de CSV

```csv
companyName,solved,userState,userCity,id,created,modified,read,problemType,otherProblemType,productType,otherProductType,status,companyShortname,title,description,hasReply,category,slug,url,full_description
Ligga Telecom,,PR,São José dos Pinhais,E5Ut28jm-wOsIpVM,2022-06-30T14:55:13,2022-06-30T14:55:13,1,Cancelamento,,Internet para casa,,PENDING,ligga-telecom,"Internet nao instalada e cobrada","Pedimos transferência de internet mediante consulta de confirmação do endereco novo a disponibilidade imediata do Batel para centr",,,,,,internet-nao-instalada-e-cobrada_E5Ut28jm-wOsIpVM,https://www.reclameaqui.com.br/ligga-telecom/internet-nao-instalada-e-cobrada_E5Ut28jm-wOsIpVM/,"Pedimos transferência de internet mediante consulta de confirmação do endereco novo a disponibilidade imediata do Batel para centro de Sao Jose dos Pinhais ao confirmar a transferência foi informado da indisponibilidade no futuro endereço o que gerou cancelamento e imediata instalação por parte da Oi tal qual suroresa foi lancado na conta um saldo de R$ 1156,00 referindo se ao novo contrato? Fica dika essa estou fora akem de vender o que nao tem ? Nao entrega? E te multa? "
Ligga Telecom,,SC,Irineópolis,hDTekGlXXK8e49pm,2022-06-30T14:17:19,2022-06-30T14:17:19,1,Cancelamento,,Internet para casa,,PENDING,ligga-telecom,"NÃO CONSIGO CONTATO COM A EMPRESA PARA CANCELAR","Eu não estou conseguindo entrar em contato com a empresa para cancelar meu plano com eles. Tentei pelo número 0800 41 41 810 e pel",,,,,,no-consigo-contato-com-a-empresa-para-cancelar_hDTekGlXXK8e49pm,https://www.reclameaqui.com.br/ligga-telecom/no-consigo-contato-com-a-empresa-para-cancelar_hDTekGlXXK8e49pm/,"Eu não estou conseguindo entrar em contato com a empresa para cancelar meu plano com eles. Tentei pelo número 0800 41 41 810 e pelo 0800 da Ouvidoria mas em ambos os números diz que &quot;esse número está programado para não receber esse tipo de chamada&quot;. Tentei pelo chat online e NADA também. O único local que consegui contato foi pelo Whatsapp Comercial do qual não adiantou nada pois quando seleciono para alterar plano ou acionar suporte ou cancelamento, o bot me encaminha pra ligar pro 0800 41 41 810. EU PRECISO CANCELAR O PLANO!"
```