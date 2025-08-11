# Oficina SOAT

_Tech challenge_ da pós tech em arquitetura de software - FIAP

# Alunos

- Felipe
    - RM: `365154`
    - LinkedIn: [@felipeoli7eira](`https://www.linkedin.com/in/felipeoli7eira`)
- Nicolas
    - RM: `000000`
    - LinkedIn: [@userName](`https://google.com`)
- William
    - RM: `365973`
    - LinkedIn: [@userName](`https://google.com`)

# Material
- [Documentação DDD](https://google.com)
- [Vídeo de apresentação](https://google.com)
- [Documento de entrega - PDF](https://google.com)

# Sobre o projeto
Este projeto foi desenvolvido com [Laravel](https://laravel.com), [nginx](https://nginx.org) e [postgresql](https://www.postgresql.org) e por volta dessas 3 tecnologias, está o [docker](https://www.docker.com)/[docker compose](https://docs.docker.com/compose) para containerizar/orquestrar tudo.

O Laravel foi escolhido por ser um dos principais (se não o principal) framework PHP atualmente, e por suas facilidades para criar APIs **RESTful** de verdade, com o mínimo de esforço. Com ele conseguimos alcançar a [excelência do modelo de maturidade REST](https://mundoapi.com.br/destaques/alcancando-a-excelencia-do-rest-com-um-modelo-de-maturidade-eficiente/). Além disso, são mais de 10 anos no campo de batalha, comprovando sua eficiência e segurança, além de uma grande comunidade e um ecossistema que não para de crescer.


O **Nginx** foi escolhido como servidor web por sua [arquitetura assíncrona orientada a eventos](https://nginx.org/en/docs/http/ngx_http_core_module.html), que permite lidar com milhares de conexões simultâneas consumindo poucos recursos do sistema.
Diferente do Apache em seus modos mais tradicionais (como o MPM prefork, que cria um processo por conexão), o Nginx adota um modelo de worker processes, onde cada processo é capaz de gerenciar milhares de conexões de forma não bloqueante, por meio de I/O assíncrono. Isso o torna altamente eficiente em ambientes com alta concorrência. Embora o Apache também tenha evoluído e ofereça um modo event mais moderno, o Nginx ainda é amplamente preferido em contextos de alta performance.
Além disso, sua configuração tende a ser mais simples e direta para casos como servir arquivos estáticos, atuar como _reverse proxy_ para aplicações PHP-FPM, fazer load balancing ou cache de conteúdo.
Essa eficiência e flexibilidade explicam sua ampla adoção por [grandes empresas como Netflix, Airbnb e Dropbox](https://www.nginx.com/case-studies/), que o utilizam para escalar aplicações em ambientes de alta demanda.


O **PostgreSQL** é uma escolha de longo prazo segura, [preparada para o futuro](https://www.enterprisedb.com/blog/postgres-developers-favorite-database-2024?lang=en). O que o destaca é a [maneira como ele lida com tarefas básicas e complexas](https://www.nucamp.co/blog/coding-bootcamp-backend-with-python-2025-postgresql-vs-mysql-in-2025-choosing-the-best-database-for-your-backend) - desde armazenamento simples de dados até recursos avançados, como tratamento de dados geoespaciais e suporte nativo a JSON. Postgres [virou líder em 6 anos](https://survey.stackoverflow.co/2024/technology#1-databases), saindo de 33% para 49% de uso vs MySQL que caiu de 59% para ~40%. Nós o escolhemos por sua [escalabilidade, extensibilidade, licença e outros](https://www.bytebase.com/blog/postgres-vs-mysql/).

# Setup

Como especificado no arquivo [docker-compose.yaml](./docker-compose.yml), um container de postgres será criado na porta padrão (5432) com mapeamento `5432:5432` (host:container). É importante que você prepare o seu ambiente, liberando as portas necessárias para cada serviço. Além disso, um container de nginx será criado e iniciado no seguinte mapeamento de portas: `8080:80` (host:container). O nginx está configurado para fazer o proxy reverso para o container de php (veja o arquivo [default.conf](./build/nginx/conf.d/default.conf) para mais detalhes). É pelo container de nginx que a aplicação é acessada, então quando tudo estiver pronto, você poderá acessar `localhost:8080/api/documentation`.

Clone este repositório
```sh
git clone git@github.com:felipeoli7eira/oficina-soat.git
```

Entre na pasta criada
```sh
cd oficina-soat
```

Suba os containers
```sh
docker compose up -d --build
```

Todos os endpoints estão protegidos por um middleware que exige que um token JWT válido seja informado no header das requisições. Para conseguir um token JWT, é preciso usar o endpoint `POST` `/api/usuario/auth/autenticar` na collection "Autenticação". Ele é o único endpoint que não exige JWT. Siga o exemplo de payload e forneça um usuário e senha válidos.

> Atenção: Quando o build da aplicação Laravel acontece, alguns seeders rodam para criar alguns dados iniciais no banco de dados. Entre esses vários dados, estão alguns usuários que podem ser usados para realizar login e obter um JWT. O usuário `atendente@example.com` com a senha `senha8caracteres` é um exemplo.

Usuários preparados propositalmente que podem gerar um JWT de autenticação:

- Atendente: `atendente@example.com` e `senha8caracteres`
- Comercial: `comercial@example.com` e `senha8caracteres`
- Mecânico: `mecanico@example.com` e `senha8caracteres`
- Gestor de Estoque: `gestor_estoque@example.com` e `senha8caracteres`
