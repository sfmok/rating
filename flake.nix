{
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
    devenv.url = "github:cachix/devenv";
    phps.url = "github:loophp/nix-shell";
    systems.url = "github:nix-systems/default";
  };

  outputs = inputs @ {
    flake-parts,
    systems,
    ...
  }:
    flake-parts.lib.mkFlake {inherit inputs;} {
      imports = [inputs.devenv.flakeModule];
      systems = import systems;

      perSystem = {system, ...}: let
        pkgs = import inputs.nixpkgs {
          inherit system;
          overlays = [inputs.phps.overlays.default];
        };

        php = pkgs.api.buildPhpFromComposer {
          php = pkgs.php82;
          src = inputs.self;
          withExtensions = ["xdebug" "opcache"];
        };
        packages = [
          php
          php.packages.composer
          pkgs.symfony-cli
        ];
      in {
        formatter = pkgs.alejandra;
        devenv.shells = {
          default = {
            # https://devenv.sh/reference/options/
            inherit packages;
            dotenv.disableHint = true;
            processes.symfony.exec = "symfony serve";
            services = {
              mysql = {
                enable = true;
                package = pkgs.mysql80;
                ensureUsers = [
                  {
                    name = "vico_rating";
                    password = "vico_rating";
                    ensurePermissions = {
                      "vico_rating.*" = "ALL PRIVILEGES";
                    };
                  }
                ];

                initialDatabases = [
                  {name = "vico_rating";}
                ];
              };
            };
          };
        };
      };
    };
}
