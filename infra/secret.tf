resource "kubernetes_secret" "lab_secret" {
  metadata {
    name = "tf-lab-secret"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }
  type = "Opaque"
  data = {
    DB_USERNAME = "cG9zdGdyZXM=" 
    DB_PASSWORD = "cG9zdGdyZXM="     
    DB_NAME = "cG9zdGdyZXM="     
  }
}