resource "kubernetes_deployment" "lab_soat_nginx" {
  metadata {
    name = "lab-soat-nginx"
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    selector {
      match_labels = {
        app = "lab-soat-nginx"
      }
    }
    template {
      metadata {
        labels = {
          app = "lab-soat-nginx"
        }
      }
      spec {
        container {
          name  = "lab-soat-nginx"
          image = "wllsistemas/nginx_lab_soat:${var.nginx_image_tag}"
          port {
            container_port = 80
          }
        }
      }
    }
  }
}