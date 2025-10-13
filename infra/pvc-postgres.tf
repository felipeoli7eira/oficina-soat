resource "kubernetes_persistent_volume_claim" "lab_postgres_pvc" {
  metadata {
    name      = "lab-postgres-pvc"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    access_modes = ["ReadWriteOnce"]
    resources {
      requests = {
        storage = "1Gi"
      }
    }
    storage_class_name = ""
  }
}