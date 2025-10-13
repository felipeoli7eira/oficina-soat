resource "kubernetes_persistent_volume" "lab_local_storage_pv" {
  metadata {
    name = "lab-local-storage"
  }

  spec {
    storage_class_name = "manual"
    capacity = {
      storage = "1Gi"
    }
    access_modes = ["ReadWriteOnce"]
    persistent_volume_reclaim_policy = "Retain"
    persistent_volume_source {
      host_path {
        path = "/mnt/minikube/directory/structure/"
        type = "DirectoryOrCreate"
      }
    }
  }
}