# Oficina SOAT

_Tech challenge_ da pós tech em arquitetura de software - FIAP Fase 2

# Alunos

- Felipe
    - RM: `365154`
    - discord: `felipeoli7eira`
    - LinkedIn: [@felipeoli7eira](https://www.linkedin.com/in/felipeoli7eira)
- Nicolas
    - RM: `365746`
    - discord: `nic_hcm`
    - LinkedIn: [@Nicolas Martins](https://www.linkedin.com/in/nicolas-henrique/)
- William
    - RM: `365973`
    - discord: `wllsistemas`
    - LinkedIn: [@William Francisco Leite](https://www.linkedin.com/in/william-francisco-leite-9b3ba9269/?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app)

# Material
- [Vídeo de apresentação](https://www.youtube.com/watch?v=POC_FaWt39E)
- [Documento de entrega - PDF](https://drive.google.com/file/d/1Xl_8YgZHRIELfM3yCWjbswp4tD7Gxoin/view?usp=drive_link)

# Sobre o projeto
Este projeto foi desenvolvido com [Laravel](https://laravel.com), [nginx](https://nginx.org) e [postgresql](https://www.postgresql.org) e por volta dessas 3 tecnologias, está o [docker](https://www.docker.com)/[docker compose](https://docs.docker.com/compose) e toda uma arquitetura com kubernetes que entraremos em mais detalhes em seções posteriores.


O Laravel foi escolhido por ser um dos principais (se não o principal) framework PHP atualmente, e por suas facilidades para criar APIs **RESTful** de verdade, com o mínimo de esforço. Com ele conseguimos alcançar a [excelência do modelo de maturidade REST](https://mundoapi.com.br/destaques/alcancando-a-excelencia-do-rest-com-um-modelo-de-maturidade-eficiente/). Além disso, são mais de 10 anos no campo de batalha, comprovando sua eficiência e segurança, além de uma grande comunidade e um ecossistema que não para de crescer.


O **Nginx** foi escolhido como servidor web por sua [arquitetura assíncrona orientada a eventos](https://nginx.org/en/docs/http/ngx_http_core_module.html), que permite lidar com milhares de conexões simultâneas consumindo poucos recursos do sistema.
Diferente do Apache em seus modos mais tradicionais (como o MPM prefork, que cria um processo por conexão), o Nginx adota um modelo de worker processes, onde cada processo é capaz de gerenciar milhares de conexões de forma não bloqueante, por meio de I/O assíncrono. Isso o torna altamente eficiente em ambientes com alta concorrência. Embora o Apache também tenha evoluído e ofereça um modo event mais moderno, o Nginx ainda é amplamente preferido em contextos de alta performance.
Além disso, sua configuração tende a ser mais simples e direta para casos como servir arquivos estáticos, atuar como _reverse proxy_ para aplicações PHP-FPM, fazer load balancing ou cache de conteúdo.
Essa eficiência e flexibilidade explicam sua ampla adoção por [grandes empresas como Netflix, Airbnb e Dropbox](https://www.nginx.com/case-studies/), que o utilizam para escalar aplicações em ambientes de alta demanda.


O **PostgreSQL** é uma escolha de longo prazo segura, [preparada para o futuro](https://www.enterprisedb.com/blog/postgres-developers-favorite-database-2024?lang=en). O que o destaca é a [maneira como ele lida com tarefas básicas e complexas](https://www.nucamp.co/blog/coding-bootcamp-backend-with-python-2025-postgresql-vs-mysql-in-2025-choosing-the-best-database-for-your-backend) - desde armazenamento simples de dados até recursos avançados, como tratamento de dados geoespaciais e suporte nativo a JSON. Postgres [virou líder em 6 anos](https://survey.stackoverflow.co/2024/technology#1-databases), saindo de 33% para 49% de uso vs MySQL que caiu de 59% para ~40%. Nós o escolhemos por sua [escalabilidade, extensibilidade, licença e outros](https://www.bytebase.com/blog/postgres-vs-mysql/).

# Documentação sobre infra

## Desenho da Arquitetura

![clean-arch.png](./docs/img/arquitetura-kubernetes.png)

## 🐳 Deploy da Aplicação

- Foram escritos 2 arquivos Dockerfile que estão na pasta **./build/backend**
    1. **nginx**
    2. **php:8.4-fpm-alpine3.22**

> [!NOTE]
> O container PostgreSQL é criado a partir de uma imagem no Docker Hub **postgres:17.5**.

> [!NOTE]
> O banco de dados da aplicação é criado após o container do **PHP** ser executado, as rotinas de **migrations** e **seeders** são executadas via comando `artisan` do laravel durante a inicialização do container, através do script **/build/backend/startup.sh**.


#### Build Imagem Docker
- **Nginx**: executar comando à partir da raiz do projeto
```bash
  docker build -t wllsistemas/nginx_lab_soat:fase2 -f build/backend/Dockerfile-nginx .
```
- **PHP + Código Fonte**: executar comando à partir da raiz do projeto
```bash
  docker build -t wllsistemas/php_lab_soat:fase2 -f build/backend/Dockerfile .
```

## ☸️ kubernetes

Todos os manifestos kubernetes estão dentro da pasta **./k8s**, os manifestos foram nomeados para facilitar a ordem de execução.

#### Arquivos de Manifesto
```bash
  00-metrics-server.yaml **
  01-namespace.yaml
  02-configmap.yaml
  03-secret.yaml
  04-secret-postgres.yaml
  05-pv-postgres.yaml
  06-pvc-postgres.yaml
  07-svc-postgres.yaml
  08-svc-php.yaml
  09-svc-nginx.yaml
  10-pod-postgres.yaml
  11-pod-php.yaml
  12-pod-nginx.yaml
  13-hpa-ngix.yaml
```
### Namespace kubernetes
Para melhor organização do ambiente, todos os manifestos são criados dentro do namespace **lab-soat** através do manifesto **01-namespace.yaml**.

### Pré-requisitos
- docker >= 28.4.0
- kubeadm >= 1.34.1
- kubectl >= 1.32.2

### Como Executar todos os manifestos
Executar o comando abaixo à partir da raiz do projeto

```bash
  kubectl apply -f ./k8s
```

### Listando Serviços e Portas
Executar o comando abaixo à partir da raiz do projeto, passando o namespace **lab-soat**

```bash
  kubectl get services -n lab-soat
```

#### Portas de Acesso
| Service | Port | Type |
|---|---|---|
|svc-php|9000|ClusterIP|
|postgres|5432|ClusterIP|
|svc-ngix|31000|NodePort|

### URL de acesso Health Check
```bash
  http://localhost:31000/api/ping
```


### Como Deletar todo o Ambiente
Esse comando deleta todos os componentes do namespace **lab-soat**

```bash
  kubectl delete namespace lab-soat
```

> [!NOTE]
> As imagens buildadas estão no repositório [Docker Hub](https://hub.docker.com/repositories/wllsistemas)

> [!WARNING]
> O manifesto **metrics-server.yaml** foi necessário em nosso Ambiente para criação dos recursos de métricas utilizados pelo **hpa**, ele insere no args a flag abaixo.

```bash
  - --kubelet-insecure-tls
```

## 🌍 Terraform

Todos os scripts **Terraform** estão dentro da pasta **./infra**.

### Pré-requisitos
- docker >= 28.4.0
- kubeadm >= 1.34.1
- kubectl >= 1.32.2
- terraform >= 1.13.3

### Recursos do Cluster

> [!NOTE]
> É necessário criar recursos de métricas em nível de cluster, esses recursos estão na subpasta **./infra/cluster_base** e precisam ser criados apenas na primeira execução.

#### Navegar até o diretório dos scripts
```bash
  cd infra/cluster_base
```

#### Inicializar terraform
```bash
  terraform init
```

#### Executar comando de análise do código
```bash
  terraform plan
```

#### Como Executar todos os scripts
```bash
  terraform apply
```

### Recursos da Aplicação

> [!NOTE]
> Recursos da aplicação setão na pasta **./infra** e podem ser destruídos com o comando `destroy`.

#### Navegar até o diretório dos scripts
```bash
  cd infra
```

#### Inicializar terraform
```bash
  terraform init
```

#### Executar comando de análise do código
```bash
  terraform plan -var="php_image_tag=fase2" -var="nginx_image_tag=fase2"
```

#### Como Executar todos os scripts
Executar o comando abaixo, passando como parâmetro o valor das variáveis contendo as TAGs das imagens no Docker Hub.

```bash
  terraform apply -auto-approve -var="php_image_tag=fase2" -var="nginx_image_tag=fase2"
```

#### Como Deletar todo o Ambiente
Esse comando deleta todos os componentes

```bash
  terraform destroy -auto-approve -var="php_image_tag=fase2" -var="nginx_image_tag=fase2"
```

## 📈 HPA (HorizontalPodAutoscaler)
Escrevemos um manifesto kubernetes `13-hpa-nginx.yaml` para automatizar o escalonamento horizontal dos pods **lab-soat-nginx** com base em métricas de utilização.

| Métrica | Valor | Und Medida |
|---|---|---|
| Utilização de CPU | 10 | % |
|Média de Consumo Memória RAM| 10 | MegaBytes |

O HPA garante que o Deployment **lab-soat-nginx** tenha entre 1 e 10 pods, escalando para cima se a utilização média da CPU exceder 10% (em relação ao request do pod) ou se o consumo médio de memória exceder 10Mi. O objetivo é manter a performance da aplicação otimizada, adicionando ou removendo pods conforme a demanda, sem intervenção manual

## 🚀 Pipeline GitHub Actions

#### 1. Aprovação de um PR para merge com a `main`
No branch `main` são efetuados merges mediante aprovação dos PRs.

#### 2. Execução da Pipeline CI
Ao executar o merge, é disparada a pipeline `ci.yaml` que executa:
- Testes Unitários e Integração
- Build da Imagem no Docker Hub
- Envia e-mail customizado em caso de Sucesso ou Falha

#### 3. Execução da Pipeline CD
Após a execução da pipeline CD , é disparada a pipeline `cd.yaml` que executa:
- Valida a execução da pipeline CI
- Copia os manifestos kubernetes para VPS
- Aplica os manifestos na VPS, atualizando aplicação
- Envia e-mail customizado em caso de Sucesso ou Falha

# Setup local

Antes de fazer o clone do projeto, precisamos ter em mente algumas coisas:

Como especificado no arquivo [docker-compose.yaml](./docker-compose.yml), um container de postgres será criado na porta padrão (`5432`) com mapeamento `5432:5432` (`host:container`).

O nginx está configurado para fazer o proxy reverso para o container de php (veja o arquivo [nginx.conf](./build/server/nginx.conf) para mais detalhes). É pelo container de nginx que a api é acessada, então quando tudo estiver pronto, você poderá acessar `http://localhost:8080/api` e como teste rápido, acessar o endpoint `http://localhost:8080/api/ping`. O resultado esperado é a seguinte response:

```json
{
  "msg": "pong",
  "err": false
}
```

É importante que você esteja certo de que as portas `5432` e `8080` no seu computador estejam liberadas para que esses serviços sejam alocados corretamente nelas. Caso contrário, certamente erros irão ocorrer. Uma alternativa será editar o [docker-compose.yaml](./docker-compose.yml) mudando as portas de host dos serviços, para portas que estejam liberadas na sua máquina.


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

O resultado esperado é que 3 containers estejam em pleno funcionamento:
- php (9000/tcp)
- nginx (0.0.0.0:8080->80/tcp)
- postgres (0.0.0.0:5432->5432/tcp)

Agora, como dito anteriormente, você pode tentar acessar o endpoint `http://localhost:8080/api/ping` e verificar se a api responde com "pong".

# Testes

O projeto conta com testes unitários e de integração desenvolvidos com PHPUnit. Os testes garantem a qualidade e confiabilidade do código, cobrindo desde a lógica de domínio até a persistência de dados.

## Executando os Testes Localmente

Com os containers em execução, você pode rodar os testes usando o seguinte comando:

```sh
docker compose exec php php artisan test
```

Para executar os testes com relatório de cobertura:

```sh
docker compose exec php php artisan test --coverage
```

Para uma visualização mais compacta:

```sh
docker compose exec php php artisan test --coverage --compact
```

![testes.png](./docs/img/testes.png)

### Relatório de Cobertura HTML

Ao executar os testes com a flag `--coverage`, um relatório HTML detalhado é gerado automaticamente na pasta `backend/var/coverage`. Para visualizar:

1. Abra o arquivo `backend/var/coverage/index.html` no seu navegador
2. Navegue pelas classes e métodos para ver detalhes da cobertura linha por linha

Além do HTML, também é gerado um arquivo texto com resumo em `backend/var/coverage.txt`.

## Estrutura dos Testes

Os testes estão organizados em duas categorias:

- **Testes Unitários** (`tests/Unit`): Validam a lógica de negócio das entidades e casos de uso do domínio
- **Testes de Integração** (`tests/Feature`): Testam a interação entre as camadas da aplicação, incluindo repositórios e controllers

## Configuração

A configuração dos testes está definida no arquivo `backend/phpunit.xml`, que especifica:
- Conexão com PostgreSQL para testes de integração
- Configurações de ambiente de teste
- Diretórios de cobertura de código

# API Documentation

O [postman](https://www.postman.com) foi usado para criar a documentação da API. O workspace com a collection está [disponível aqui](https://app.getpostman.com/join-team?invite_code=a8f7c5db50618a4d057b1e50ca129cef16d68fbd74f03c9d4f532c18e9fff4c3&target_code=0249e09988430bb18a9413c8067664c2). Você notará que cada recurso está organizado em pastas:

- pasta `usuario`: CRUD de usuários do sistema (mecânicos, atendentes, etc...)
- pasta `servico`: CRUD de serviços da oficina, como troca de óleo, revisões e etc...
- pasta `material: peça / insumo`: CRUD de materiais (peças e insumos) usados nas ordens de serviço, como pastilha de freio, filtros de ar e óleo, etc...
- pasta `cliente`: CRUD de clientes da oficina
- pasta `veiculo`: CRUD de veículos dos clientes
- pasta `ordem`: CRUD da ordem de serviço
- pasta `auth`: Autenticação de usuários do sistema.

No momento da inicialização dos containers mapeados no [docker-compose.yaml](./docker-compose.yml), a base de dados é populada com um usuário de teste, como descrito no arquivo [DatabaseSeeder.php](./backend/database/seeders/DatabaseSeeder.php). Você pode usar os dados desse usuário para obter um token JWT e testar todo os fluxos da API. Os dados desse usuário são os seguintes:

- Usuário: `soat@example.com`
- Senha: `padrao`

A pasta `auth` contém um único endpoint nomeado "login". É esse endpoint que você vai usar para obter um token JWT. O postman nos oferece algumas features muito legais, uma delas é a execução de scripts pré e pós request. O endpoint "login" dentro da pasta `auth` tem um script pós requisição, que basicamente pega o token jwt devolvido, e salva na variável de ambiente "token".

![postman-postscripts.png](./docs/img/postman-postscripts.png)

Sendo assim, você não precisa copiar o token devolvido, ir nas variáveis de ambiente e colar como valor. Isso é feito automaticamente na devolução dele na responde do endpoint.

Falando em variáveis de ambiente, todas as variáveis de ambiente estão em contexto de collection. Isso significa que a collection quando exportada ou importada (que não é o caso aqui) já vai com as variáveis junto.

![variaveis-postman.png](./docs/img/variaveis-postman.png)

A maior parte dos endpoints da API, estão protegidos por um middleware que exige que um token JWT válido seja informado, exceto o endpoint `/ping` que é para teste rápido, e os endpoints de aprovação ou reprovação de uma ordem de serviço informada como parâmetro de URL, conforme exemplificado nos endpoints:

![variaveis-postman.png](./docs/img/endpoints-aprovacao-desaprovacao.png)

Um último ponto sobre a documentação da API, é que ela tem exemplos de resposta de sucesso e erro para cada endpoint:

![variaveis-postman.png](./docs/img/ver-exemplo-de-respostas.png)

Clique nas setinhas em cada endpoint para ver os exemplos de respostas:

![variaveis-postman.png](./docs/img/exemplo-respostas-aberto.png)

# Fluxo principal da API
Sem dúvidas, o fluxo principal da API é o cadastro e gestão de ordens de serviço. Nosso fluxo de cadastro de ordens funciona da seguinte forma:

- Cadastro da ordem: informando somente uuid do cliente e veículo
- Cadastro de materiais (peças e insumos) na OS: use o endpoint dentro de `ordem/ordem-material/adiciona material`
- Cadastro de serviços na OS: use o endpoint dentro de `ordem/ordem-servico/adiciona servico`

Feito isso, a ordem estará montada com os materiais necessários e serviços que serão executados. Feito isso, as próximas ações a serem tomadas, são de atualização de status. Para isso use o endpoint `ordem/update status`.

# Clean architecture

O projeto foi organizado usando clean architecture. Essa organização pode ser vista dentro da pasta `backend/app` e vamos entrar em detalhesa agora.

- Entidades e casos de uso: `backend/app/Domain`
- Controllers (da clean arch): `backend/app/Infrastructure/Controller`
- Gateway: `backend/app/Infrastructure/Gateway`
- Presenters: `backend/app/Infrastructure/Presenters`
- Camadas mais externas:
    - Banco de dados: `backend/app/Infrastructure/Repositories`
    - Web: `backend/app/Http/`

Algumas boas práticas e padrões foram adotados para desenvoler o projeto, como por exemplo clean code, SOLID, Repository pattern e Object Calistenics.

Nossos métodos possuem nomes simples e claros, que demonstram o que fazem, como por exemplo:
```php
public function validarNome(): void
{
    if (strlen(trim($this->nome)) < 3) {
        throw new InvalidArgumentException('Nome deve ter pelo menos 3 caracteres');
    }
}
```

Codificamos para interfaces e não para implementações concretas, como é o caso da interface que gera o JWT:
```php
interface TokenServiceInterface
{
    public function generate(array $claims): string;
    public function validate(string $token): ?JsonWebTokenFragment;
    public function refresh(string $token): string;
    public function invalidate(string $token): void;
}
```

Nossas dependências são de fora para dentro:

![clean-arch.png](./docs/img/clean-arch.png)

Nossas regras de negócio estão seguras nos _use cases_ e _entities_, conforme deve ser.
