# quiz
Quiz é uma classe PHP para exibição e verificação de testes de personalidade de multipla opção onde a resposta é computada a partir das opções selecionadas.

Principais características:
+ Suporte a armazenamento de questões em arquivo XML ou array associativo.
+ Embaralhamento de questões, para evitar que as questões sejam exibidas sempre na mesma ordem.
+ Embaralhamento de opções, para evitar que as questões sigam padrões que permitam o usuário utilizar algum padrão,
escolher sempre a segunda opção pr exemplo.
+ Permite persistir os dados do objeto, facilitando a implementação de um cache, evitando ter que ler ou acessar a fonte das questões toda vez que utilizar.
+ Verificação das respostas fornecidas.

Em caso de empate na verificação dos resultados, é vencedora a questão que foi escolhida por último pelo usuário.

## Iniciando
Essas instruções vão auxiliar você no processo de instalação da versão de demonstração no seu servidor.

### Pré-requisitos
O código atual requer PHP 5.4 ou superior e módulo *simplexml* instalado.
Os testes phpUnit requerem a versão 5.6.30 ou superior.

### Instalação Demonstração
Para instalar o demo, basta você copiar todos os arquivos do repositório em um diretório vazio do servidor com suporte a PHP 5.4 ou superior.

```
cd /caminho/para/seu/public_html/diretorio/

git clone https://github.com/gjantsch/quiz.git
```

e acessar pelo navegador:

```
http://servidor/diretorio/quiz/
````

### Utilização da classe
O código de demonstração é autoexplicativo.

## Autor

* **Gustavo Jantsch** - [gjantsch](https://github.com/gjantsch)


## Licensa

This project is licensed under the GNU General Public License version 3.

