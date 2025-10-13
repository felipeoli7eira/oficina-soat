resource "kubernetes_service" "svc_php" {
  metadata {
    name = "svc-php"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    type = "ClusterIP"
    selector = {
      app = "lab-soat-php"
    }
    port {
      port        = 9000 # Porta que o Service expõe internamente
      target_port = 9000 # Porta na qual o container PHP-FPM está escutando
    }
  }
}