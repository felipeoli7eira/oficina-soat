resource "kubernetes_namespace" "lab_soat_terraform" {
  metadata {
    name = "tf-lab-soat"
  }
}