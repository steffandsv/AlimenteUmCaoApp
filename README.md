# AlimenteUmCaoApp

## DEMO DO PAINEL:

![Pagina inicial pós-login](https://i.imgur.com/122gR0a.jpg)

![Editar/Adicionar entradas](https://i.imgur.com/0Fhv1nJ.jpg)


## DEMO DO APP:

![Mapa apontando os locais](https://i.imgur.com/nU24PxA.jpg)

![Informações ao clicar no marcador](https://i.imgur.com/NeL1qaf.jpg)

![Pagina de Novidades e Publicidade](https://i.imgur.com/09TzBtM.jpg)

## Como Instalar e Configurar

Crie uma database e rode nela a query contida em "/Painel Backend/rest-api.sql"

Adicione as configurações do seu servidor SQL nos parâmetros contidos nas primeiras linhas dos arquivos:</br>
/Painel Backend/index.php</br>

/Painel Backend/rest-api.php</br>

O usuário e a senha padrão para login no painel são:</br>
admin</br>
admin</br> (podem ser alterados modificando-se as linhas 12 e 13 do arquivo /Painel Backend/index.php

Após a correta instalação da rest-api e do painel, adicione o endereço online da instalação no lugar da expressão "seusite" contida nas linhas 372,374,758,760,1009,1111 do arquivo /Ionic App/www/js/controllers.js

No Arquivo /Ionic App/www/index.html, adicione sua API KEY do Google Maps na linha 33, no lugar da expressão "suaAPIKey". </br>Caso ainda não tenha uma, obtenha em: https://developers.google.com/maps/documentation/javascript/get-api-key

Após isso, você já está pronto para compilar seu aplicativo utilizando Cordova ou Ionic.
Abaixo uma lista de comandos que deverá ser executada para instalar todos os plugins utilizados:

> cordova plugin add cordova-plugin-device --save </br>
> cordova plugin add cordova-plugin-console --save </br>
> cordova plugin add cordova-plugin-splashscreen --save </br>
> cordova plugin add cordova-plugin-statusbar --save </br>
> cordova plugin add cordova-plugin-whitelist --save </br>
> cordova plugin add ionic-plugin-keyboard --save </br>
> cordova plugin add cordova-plugin-dialogs --save </br>
> cordova plugin add cordova-plugin-inappbrowser --save </br>
> cordova plugin add cordova-plugin-velda-devicefeedback --save


Finalmente, adicione as plataformas para as quais você deseja compilar o app:

> cordova platform add android

ou

> cordova platform add ios

Rode os comandos de pré-compilação:

> cordova clean </br>
> cordova requirements


Compile:

> cordova build android[ou ios]



