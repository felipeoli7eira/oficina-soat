resource "kubernetes_deployment" "lab_soat_php" {
  metadata {
    name = "lab-soat-php"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    selector {
      match_labels = {
        app = "lab-soat-php"
      }
    }
    template {
      metadata {
        labels = {
          app = "lab-soat-php"
        }
      }
      spec {
        container {
          name  = "lab-soat-php"
          image = "wllsistemas/php_lab_soat:${var.php_image_tag}"
          port {
            container_port = 9000
          }
          env {
            name = "ENV_APP_ENV"
            value_from {
              config_map_key_ref {
                name = "tf-configmap"
                key  = "APP_ENV"
              }
            }
          }
          env {
            name = "ENV_APP_NAME"
            value_from {
              config_map_key_ref {
                name = "tf-configmap"
                key  = "APP_NAME"
              }
            }
          }
          env {
            name = "ENV_APP_VERSION"
            value_from {
              config_map_key_ref {
                name = "tf-configmap"
                key  = "APP_VERSION"
              }
            }
          }
          env {
            name = "DB_NAME"
            value_from {
              secret_key_ref {
                name = "tf-lab-secret"
                key  = "DB_NAME"
              }
            }
          }
          env {
            name = "DB_PASSWORD"
            value_from {
              secret_key_ref {
                name = "tf-lab-secret"
                key  = "DB_PASSWORD"
              }
            }
          }
          env {
            name = "DB_USERNAME"
            value_from {
              secret_key_ref {
                name = "tf-lab-secret"
                key  = "DB_USERNAME"
              }
            }
          }
        }
      }
    }
  }
}