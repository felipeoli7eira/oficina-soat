#!/bin/bash

set -e

echo "📁 Criando a pasta entrypoint"
mkdir -p /var/www/html/entrypoint

echo "🗃️ Criando o arquivo index.md"
echo "# Arquivo de Teste

Este arquivo foi criado pelo entrypoint do container.

Data de criação: $(date)
Container: oficina_soat_php

## Informações do Sistema
- Hostname: $(hostname)
- User: $(whoami)
- Working Directory: $(pwd)

Teste realizado com sucesso!" > /var/www/html/entrypoint/index.md

echo "✅ Arquivo /var/www/html/entrypoint/index.md criado com sucesso!"

# Lista o conteúdo criado para confirmar
echo "📁 Conteúdo do diretório /var/www/html/entrypoint:"
ls -la /var/www/html/entrypoint/

echo "🚀 Iniciando o container"

# Executa o comando passado como parâmetro (nginx)
exec "$@"
