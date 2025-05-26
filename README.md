# ButtonColorChange - Módulo de Troca de Cor dos Botões via CLI

## Descrição

Este módulo Magento 2 permite que clientes alterem dinamicamente a cor de todos os botões de uma store-view específica via linha de comando, sem necessidade de conhecimento técnico ou abertura de tickets para o time de atendimento.

Com o comando CLI criado, o cliente pode definir a cor dos botões através de um código HEX, junto com o ID da store-view desejada, facilitando a personalização diária da aparência da loja para atrair mais clientes.

---

## Funcionalidades

- Comando Magento CLI para alterar a cor dos botões de uma store-view.
- Validação do código HEX de cor para evitar erros.
- Verificação da existência da store-view informada.
- Aplicação da cor atualizada diretamente em uma tag <style></style> injetada no head da página via Observer.

---

## Como instalar

1. Clone o repositório dentro da pasta `app/code/` do seu Magento 2.4.8.

```bash
git clone https://github.com/jackson-castro/ButtonColorChange.git Project/ButtonColorChange
```


2. Execute os comandos Magento para habilitar o módulo, atualizar o setup e compilar as classes do módulo:

```bash
php bin/magento module:enable Project_ButtonColorChange
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```
---

## Como utilizar

Para alterar a cor dos botões da store-view com ID 1 para preto (#000000), execute:

```bash
php bin/magento color:change 000000 1
```

- O primeiro parâmetro é o código HEX da cor (sem o #).
- O segundo parâmetro é o ID da store-view alvo.

---

## Validações

- O comando valida se o código HEX possui 6 caracteres válidos (0-9, A-F).
- Verifica se a store-view com o ID informado existe.
- Caso algum dado seja inválido, uma mensagem de erro amigável é exibida.

---

## Detalhes de implementação

- O módulo define um comando CLI personalizado registrado via `di.xml`.
- Realiza injeção dinâmica de estilo via observer frontend no `<head></head>` da página referente ao store view id selecionado.
- O seletor CSS afeta botões comuns, ex:`button`, `.action-primary`, `.btn`.
- O comando pode ser executado quantas vezes desejar para ajustar a cor conforme a necessidade do cliente.
- Contem cobertura de teste unitários.

---

## Considerações finais

Este módulo foi desenvolvido pensando na usabilidade e autonomia do cliente final, eliminando a necessidade de intervenções técnicas para personalização diária da interface. Mas abre espaço para futuras implementações, que trariam ainda mais facilidade para o cliente. Como a criação de uma interface de configuração geral dentro do painel do admin com um colorpicker para melhor visualização e seleção das cores dos botões.
