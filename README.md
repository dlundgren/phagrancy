# Private Vagrant Box Hosting

Phagrancy implements a self-hosted subset of Vagrant Cloud. It allows you to build images in Packer, 
publish them and then share the images with your co-workers via Vagrant, all on-premise.

#### Configuration

Create a `.env` and specify the `storage_path`. If this path is not absolute then the base
path to the app will be used. The default is `data/storage`.

### Publishing images
##### Via Packer
Add something like the following to your `.json` Packer file. For Packer versions <= 0.8.2, use `server_address`, *not* `atlas_url`.
```
  ...
  "post-processors": [                              
    {   
      "output": "box/{{.Provider}}/ubuntu1404-{{user `cm`}}{{user `cm_version`}}-{{user `version`}}.box",
      "type": "vagrant"
    },  
    {   
      "type": "atlas",
      "artifact": "myusername/ubuntu",
      "artifact_type": "vagrant.box",
      "atlas_url": "http://localhost:8099/",
      "metadata": {
        "provider": "virtualbox",
        "version": "1.0.0"
      }   
    }   
  ], 
  ...
```
##### Manually uploading
You can easily upload a box you have built locally using `curl`.
```
curl -XPUT http://localhost:8099/api/v1/box/myusername/ubuntu/version/1.0.0/provider/virtualbox/upload --upload-file ubuntu-precise.box
```

### Using in Vagrant
Using Phagrancy requires a different Vagrant server URL. This can be set as an environment variable *or* as part of the `Vagrantfile`. Here is an example `Vagrantfile` with the server URL set.
```ruby
ENV['VAGRANT_SERVER_URL'] = 'http://localhost:8099'
Vagrant.configure(2) do |config|
  config.vm.box = "myusername/ubuntu"
end
```

### API
Operation| Command 
---------|----------
**Deleting a box** | `curl -XDELETE http://localhost:8099/api/v1/box/myusername/ubuntu/version/1.0.0/provider/virtualbox`
**Manually uploading a box** | `curl -XPUT http://localhost:8099/api/v1/box/myusername/ubuntu/version/1.0.0/provider/virtualbox/upload --upload-file ubuntu-precise.box`
**Listing box versions** | `curl http://localhost:8099/myusername/ubuntu`
**Deleting all box versions** | *Each box must be specifically deleted*

### Security
Phagrancy is intended to be used in a trusted network, and doesn't have any authentication. As can be seen in
the examples above, reading, writing and modifying boxes is allowed without authentication.

## Credits

The idea is based off of the [Vagrancy](https://github.com/ryandoyle/vagrancy) project, but has been updated for current packer releases.