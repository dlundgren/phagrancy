variable "api_version" {
  type    = string
  default = "8080"
}

variable "direct_upload" {
  type        = bool
  default     = true
  description = "Use direct upload"
}

variable "port" {
  type    = string
  default = "8080"
}

variable "version" {
  type    = string
  default = "changeme"
}

source "file" "static" {
  content = "kakaw"
  target  = "/dev/null"
}

build {
  sources = ["source.file.static"]

  post-processors {
    post-processor "artifice" {
      files = ["test.box"]
    }

    post-processor "vagrant-cloud" {
      access_token             = "token"
      box_tag                  = "test/test"
      insecure_skip_tls_verify = true
      no_direct_upload         = var.direct_upload
      vagrant_cloud_url        = "http://127.0.0.1:${var.port}/api/${var.api_version}"
      version                  = var.version
    }
  }
}