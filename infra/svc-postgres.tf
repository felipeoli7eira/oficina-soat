resource "kubernetes_service" "svc_postgres" {
  metadata {
    name = "postgres"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    type = "ClusterIP"
    selector = {
      app = "lab-soat-postgres"
    }
    port {
      port        = 5432 
      target_port = 5432 
    }
  }
}