resource "kubernetes_config_map" "php_app_config" {
  metadata {
    name = "tf-configmap"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  data = {
    APP_NAME    = "lab-soat"
    APP_VERSION = "1.0.0"
    APP_ENV     = "production"
  }
}