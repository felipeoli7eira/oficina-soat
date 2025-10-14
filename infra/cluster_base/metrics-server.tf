# ServiceAccount for Metrics Server
resource "kubernetes_service_account" "metrics_server" {
  metadata {
    name      = "metrics-server"
    namespace = "kube-system"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }
}

# ClusterRole for aggregated metrics reader
resource "kubernetes_cluster_role" "aggregated_metrics_reader" {
  metadata {
    name = "system:aggregated-metrics-reader"
    labels = {
      "k8s-app"                                 = "metrics-server"
      "rbac.authorization.k8s.io/aggregate-to-admin" = "true"
      "rbac.authorization.k8s.io/aggregate-to-edit"  = "true"
      "rbac.authorization.k8s.io/aggregate-to-view"  = "true"
    }
  }

  rule {
    api_groups = ["metrics.k8s.io"]
    resources  = ["pods", "nodes"]
    verbs      = ["get", "list", "watch"]
  }
}

# ClusterRole for metrics server
resource "kubernetes_cluster_role" "metrics_server_role" {
  metadata {
    name = "system:metrics-server"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  rule {
    api_groups = [""]
    resources  = ["nodes/metrics"]
    verbs      = ["get"]
  }

  rule {
    api_groups = [""]
    resources  = ["pods", "nodes"]
    verbs      = ["get", "list", "watch"]
  }
}

# RoleBinding for metrics server auth reader
resource "kubernetes_role_binding" "metrics_server_auth_reader" {
  metadata {
    name      = "metrics-server-auth-reader"
    namespace = "kube-system"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  role_ref {
    api_group = "rbac.authorization.k8s.io"
    kind      = "Role"
    name      = "extension-apiserver-authentication-reader"
  }

  subject {
    kind      = "ServiceAccount"
    name      = kubernetes_service_account.metrics_server.metadata[0].name
    namespace = kubernetes_service_account.metrics_server.metadata[0].namespace
  }
}

# ClusterRoleBinding for metrics server auth delegator
resource "kubernetes_cluster_role_binding" "metrics_server_auth_delegator" {
  metadata {
    name = "metrics-server:system:auth-delegator"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  role_ref {
    api_group = "rbac.authorization.k8s.io"
    kind      = "ClusterRole"
    name      = "system:auth-delegator"
  }

  subject {
    kind      = "ServiceAccount"
    name      = kubernetes_service_account.metrics_server.metadata[0].name
    namespace = kubernetes_service_account.metrics_server.metadata[0].namespace
  }
}

# ClusterRoleBinding for metrics server role
resource "kubernetes_cluster_role_binding" "system_metrics_server" {
  metadata {
    name = "system:metrics-server"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  role_ref {
    api_group = "rbac.authorization.k8s.io"
    kind      = "ClusterRole"
    name      = kubernetes_cluster_role.metrics_server_role.metadata[0].name
  }

  subject {
    kind      = "ServiceAccount"
    name      = kubernetes_service_account.metrics_server.metadata[0].name
    namespace = kubernetes_service_account.metrics_server.metadata[0].namespace
  }
}

# Service for Metrics Server
resource "kubernetes_service" "metrics_server" {
  metadata {
    name      = "metrics-server"
    namespace = "kube-system"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  spec {
    port {
      name        = "https"
      port        = 443
      protocol    = "TCP"
      target_port = "https"
    }
    selector = {
      "k8s-app" = "metrics-server"
    }
  }
}

# Deployment for Metrics Server
resource "kubernetes_deployment" "metrics_server" {
  metadata {
    name      = "metrics-server"
    namespace = "kube-system"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  spec {
    selector {
      match_labels = {
        "k8s-app" = "metrics-server"
      }
    }
    strategy {
      rolling_update {
        max_unavailable = "0"
      }
    }
    template {
      metadata {
        labels = {
          "k8s-app" = "metrics-server"
        }
      }
      spec {
        container {
          args = [
            "--cert-dir=/tmp",
            "--secure-port=4443",
            "--kubelet-preferred-address-types=InternalIP,ExternalIP,Hostname",
            "--kubelet-use-node-status-port",
            "--metric-resolution=15s",
            "--kubelet-insecure-tls",
            # "--tls-generate-serving-cert=true", # gera problemas em cluster local
          ]
          image           = "registry.k8s.io/metrics-server/metrics-server:v0.6.4"
          image_pull_policy = "IfNotPresent"
          name            = "metrics-server"
          port {
            container_port = 4443
            name           = "https"
            protocol       = "TCP"
          }
          liveness_probe {
            http_get {
              path   = "/livez"
              port   = "https"
              scheme = "HTTPS"
            }
            failure_threshold = 3
            period_seconds    = 10
          }
          readiness_probe {
            http_get {
              path   = "/readyz"
              port   = "https"
              scheme = "HTTPS"
            }
            initial_delay_seconds = 20
            failure_threshold     = 3
            period_seconds        = 10
          }
          resources {
            requests = {
              cpu    = "100m"
              memory = "200Mi"
            }
          }
          security_context {
            allow_privilege_escalation = false
            read_only_root_filesystem  = true
            run_as_non_root            = true
            run_as_user                = 1000
          }
          volume_mount {
            mount_path = "/tmp"
            name       = "tmp-dir"
          }
        }
        node_selector = {
          "kubernetes.io/os" = "linux"
        }
        priority_class_name  = "system-cluster-critical"
        service_account_name = kubernetes_service_account.metrics_server.metadata[0].name
        volume {
          empty_dir {}
          name = "tmp-dir"
        }
      }
    }
  }
}

# APIService for metrics.k8s.io
resource "kubernetes_api_service" "metrics_k8s_io" {
  metadata {
    name = "v1beta1.metrics.k8s.io"
    labels = {
      "k8s-app" = "metrics-server"
    }
  }

  spec {
    group                  = "metrics.k8s.io"
    group_priority_minimum = 100
    insecure_skip_tls_verify = true
    service {
      name      = kubernetes_service.metrics_server.metadata[0].name
      namespace = kubernetes_service.metrics_server.metadata[0].namespace
    }
    version         = "v1beta1"
    version_priority = 100
  }
}