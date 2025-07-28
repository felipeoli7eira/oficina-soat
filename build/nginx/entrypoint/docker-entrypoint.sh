#!/bin/bash

set -e

echo "=== Executando entrypoint customizado ==="

# Cria o diretório test se não existir
mkdir -p /var/www/html/test

# Cria o arquivo test.md
echo "# Arquivo de Teste

Este arquivo foi criado pelo entrypoint do container.

Data de criação: $(date)
Container: oficina_soat_nginx

## Informações do Sistema
- Hostname: $(hostname)
- User: $(whoami)
- Working Directory: $(pwd)

Teste realizado com sucesso!" > /var/www/html/test/test.md

echo "✅ Arquivo /var/www/html/test/test.md criado com sucesso!"

# # Lista o conteúdo criado para confirmar
echo "📁 Conteúdo do diretório /var/www/html/test:"
ls -la /var/www/html/test/

echo "=== Iniciando nginx ==="

# Executa o comando passado como parâmetro (nginx)
exec "$@"
