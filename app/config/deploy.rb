set :application, "Reurbano"
set :domain,      "107.20.169.18"
set :deploy_to,   "/home/nginx/domains/reurbano.com.br"

# Deploy strategy
# set :deploy_via,      :rsync_with_remote_cache
set :user,        "reurbano"

set :app_path,    "app"

set :repository,  "git@github.com:startupsaopaulo/reurbano.git"
set :scm,         :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, `subversion` or `none`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain                         # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Rails migrations will run

set  :keep_releases,  3

set  :use_sudo,      false

# Update vendors during the deploy
set :update_vendors,  true

set :dump_assetic_assets, true

# set :git_enable_submodules, 1

# Set some paths to be shared between versions
set :shared_files,    ["app/config/parameters.yml"]
set :shared_children, [app_path + "/logs", web_path + "/uploads"]

#namespace :deploy do
# desc "Atualiza vendors.sh"
#  task :vendorssh do
#   run("cd #{latest_release} && sh bin/vendors.sh")
#  end
#end
# after 'deploy:share_childs', 'deploy:vendorssh'