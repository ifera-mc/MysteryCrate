# MysteryCrate

| HitCount | License | Poggit |
|:--:|:--:|:--:|
|[![HitCount](http://hits.dwyl.io/JackMD/MysteryCrate.svg)](http://hits.dwyl.io/JackMD/MysteryCrate)|[![GitHub license](https://img.shields.io/github/license/JackMD/MysteryCrate.svg)](https://github.com/JackMD/MysteryCrate/blob/master/LICENSE)|[![Poggit-CI](https://poggit.pmmp.io/ci.shield/JackMD/MysteryCrate/MysteryCrate)](https://poggit.pmmp.io/ci/JackMD/MysteryCrate/MysteryCrate)|

### A MysteryCrate plugin for PocketMine-MP // McPe 1.2
### Features
 - This plugin adds custom **crates** to your server.
 - Crates can be opened with a custom **key**.
 - It automatically spawns a custom **floating text** above the crate.
 - Easy to use `items.yml` for adding custom items to the crate.
 - Along with FloatingText a constant show of **Particles** is performed above the crate.
 - Upon **opening** the crate **another** set of **Particles** is generated telling player someone opened the crate.
 - Crate name can be set directly through `config.yml`.
 - Ability to give enchanted item to players.
 - Fool proof. Players cannot grief it.
 - The entire plugin is suited for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) latest API.
### How to setup?
 - This plugin depends on **[VanillaEnchantments](https://github.com/TheAz928/VanillaEnchantments)** by [@TheAz928](https://github.com/TheAz928) for adding enchants on the items. **Remember this plugin won't load without it.** So make sure to have it.
 - Get the [.phar](https://poggit.pmmp.io/ci/JackMD/MysteryCrate/MysteryCrate) and drop the into your `plugins` folder.
 - Next navigate to the `config.yml` file and mention the `XYZ` coordinates of the chest.
 - For this purpose use `/xyz` command in-game and then tap the **chest** `(ID : 54)` you want to set as a crate to get its coordinates.
 - Enter those X, Y and Z coordinates in `config.yml` under `X`, `Y` and `Z` headings.
 - **Make sure that name of the world where crate is located is same as the world folder name.**
 - Now mention the `name` of the`world` where the crate is located in `crateWorld`.
 - Reload the server and you are good to go.
 - To get the crate key use `/key [player] [amount]` in-game and then tap the crate with it.
 - To access the `xyz` locator do `/xyz` in-game.
### Commands and Permissions
|Description|Command|Permission|Default|
|:--:|:--:|:--:|:--:|
|Crate Key|`/key [playerName] [amount]`|`mc.command.key`|`op`|
|Coordinates Locator|`/xyz`|`mc.command.xyz`|`op`|
### TODO's
 - [x] Add basic particles.
 - [x] Finish working on commands.
 - [x] Add custom `items.yml`to declare custom items to be given to players.
 - [ ] Add options for more particles.
 - [ ] Make setup a bit easier by making the plugin get coordinates itself.
 - [X] Make it so that not random items are generated in all the slots as discussed [#1](https://github.com/JackMD/MysteryCrate/issues/1)
 - [ ] **(*Low Priority*)** Add support for making more than one crate. 
### Info
  - Make sure to subscribe to be updated for when i release more stuff on my [YT](https://youtu.be/x_mc-ocrdDU) channel.
  - Support is appreciated.
  - Please don't hesitate to ask questions or report bug report in issues section.
### Credits
  - [PiggyCrates](https://github.com/DaPigGuy/PiggyCrates) by [@DaPigGuy](https://github.com/DaPigGuy)
### Video
[![YouTube](https://img.youtube.com/vi/x_mc-ocrdDU/0.jpg)](https://youtu.be/x_mc-ocrdDU)
