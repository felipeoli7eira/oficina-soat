resource "kubernetes_secret" "lab_secret_postgres" {
  metadata {
    name = "lab-soat-secret-postgres"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }
  type = "Opaque"
  data = {
    POSTGRES_USER = "cG9zdGdyZXM=" 
    POSTGRES_PASSWORD = "cG9zdGdyZXM="     
    POSTGRES_DB = "cG9zdGdyZXM="     
  }
}