resource "kubernetes_horizontal_pod_autoscaler_v2" "hpa_nginx" {
  metadata {
    name = "lab-hpa-nginx" 
    namespace = kubernetes_namespace.lab_soat_terraform.metadata[0].name
  }

  spec {
    scale_target_ref {
      api_version = "apps/v1"
      kind        = "Deployment"
      name        = "lab-soat-nginx"
    }
    min_replicas = 1
    max_replicas = 10

    metric {
      type = "Resource"
      resource {
        name = "cpu"
        target {
          type               = "Utilization"
          average_utilization = 15
        }
      }
    }

    metric {
      type = "Resource"
      resource {
        name = "memory"
        target {
          type          = "AverageValue"
          average_value = "15Mi" 
        }
      }
    }
  }
}
