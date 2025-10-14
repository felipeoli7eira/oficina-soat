# Principal

# Documentação sobre infra


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
  metrics-server.yaml **
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
> É necessário criar recursos de métricas em nível de cluster, esses recursos estão na subpasta **./infra/base_cluster** e precisam ser criados apenas na primeira execução.

#### Navegar até o diretório dos scripts
```bash
  cd infra/base_cluster
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
  terraform apply -auto-approve
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
  terraform plan
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
| Utilização de CPU | 15 | % |
|Média de Consumo Memória RAM| 15 | MegaBytes |

O HPA garante que o Deployment **lab-soat-nginx** tenha entre 1 e 10 pods, escalando para cima se a utilização média da CPU exceder 15% (em relação ao request do pod) ou se o consumo médio de memória exceder 15Mi. O objetivo é manter a performance da aplicação otimizada, adicionando ou removendo pods conforme a demanda, sem intervenção manual

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


# Documentação sobre Clean Arch

[Code](./docs/code/readme.md)


# Documentação sobre testes

[Testes](./docs/tests/readme.md)
