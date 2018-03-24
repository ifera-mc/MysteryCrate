# MysteryCrate
[![HitCount](http://hits.dwyl.io/JackMD/MysteryCrate.svg)](http://hits.dwyl.io/JackMD/MysteryCrate)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/JackMD/MysteryCrate.svg)](http://isitmaintained.com/project/JackMD/MysteryCrate "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/JackMD/MysteryCrate.svg)](http://isitmaintained.com/project/JackMD/MysteryCrate "Percentage of issues still open")
[![GitHub license](https://img.shields.io/github/license/JackMD/MysteryCrate.svg)](https://github.com/JackMD/MysteryCrate/blob/master/LICENSE)

[![Poggit-Ci](https://poggit.pmmp.io/ci.shield/JackMD/MysteryCrate/MysteryCrate)](https://poggit.pmmp.io/ci/JackMD/MysteryCrate/MysteryCrate)
[![](https://poggit.pmmp.io/shield.dl.total/MysteryCrate)](https://poggit.pmmp.io/p/MysteryCrate)

### A MysteryCrate plugin for PocketMine-MP // McPe 1.2
### Features
 - This plugin adds custom **crates** to your server.
 - Crates can be opened with a custom **key**.
 - It automatically spawns a custom **floating text** above the crate.
 - Along with FloatingText a constant show of **Particles** is performed above the crate.
 - Upon **opening** the crate **another** set of **Particles** is generated telling player someone opened the crate.
 - Crate name can be set directly through `config.yml`.
 - Fool proof. Players cannot grief it.
 - The entire plugin is suited for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) latest API.
### How to setup?
 - This plugin depends on [VanillaEnchantments](https://github.com/TheAz928/VanillaEnchantments) for adding enchants on the items. **Remember this plugin won't load without it.** So make sure to have it.
 - Get the [.phar](https://poggit.pmmp.io/ci/JackMD/MysteryCrate/MysteryCrate) and drop the into your `plugins` folder.
 - Next navigate to the `config.yml` file and mention the `XYZ` coordinates of the chest.
 - For this purpose use `/xyz` command in-game and then tap the chest you want to set as a crate to get its coordinates.
 - Enter those X, Y and Z coordinates in `config.yml` under `X`, `Y` and `Z` headings.
 - **Make sure that name of the world where crate is located is same as the world folder name.**
 - Now mention the `name` of the`world` where the crate is located in `crateWorld`.
 - Reload the server and you are good to go.
 - To get the crate key use `/key [player] [amount]` in-game and then tap the crate with it.
 - To access the `xyz` locator do `/xyz` in-game.
### TODO's
 - [x] Add basic particles.
 - [x] Finish working on commands.
 - [ ] Add options for more particles.
 - [ ] Add custom `items.yml`to declare custom items to be given to players.
 - [ ] Make setup a bit easier by making the plugin get coordinates itself.
 - [ ] Make it so that not random items are generated in all the slots as discussed [#1](https://github.com/JackMD/MysteryCrate/issues/1)
 - [ ] **(*Low Priority*)** Add support for making more than one crate.
### Info
  - Make sure to subscribe to be updated for when i release more stuff on my [YT](https://youtu.be/x_mc-ocrdDU) channel.
  - Support is appreciated.
  - Please don't hesitate to ask questions or report bug report in issues section.
### Video
[![YouTube](https://img.youtube.com/vi/x_mc-ocrdDU/0.jpg)](https://youtu.be/x_mc-ocrdDU)
