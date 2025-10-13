resource "kubernetes_service" "svc_nginx" {
  metadata {
    name = "svc-nginx"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    type = "NodePort"
    selector = {
      app = "lab-soat-nginx" # Seleciona Pods com label app: lab-nginx
    }
    port {
      port        = 80    # Porta que o Service expõe internamente
      target_port = 80    # Porta na qual o container Nginx está escutando
      node_port   = 31000 # Porta que será aberta em CADA Node para acesso externo
    }
  }
}