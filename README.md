![MysteryCrate Banner](https://github.com/JackMD/MysteryCrate/blob/master/meta/MysteryCrate.png)
# MysteryCrate

| HitCount | License | Poggit | Release |
|:--:|:--:|:--:|:--:|
|[![HitCount](http://hits.dwyl.io/JackMD/MysteryCrate.svg)](http://hits.dwyl.io/JackMD/MysteryCrate)|[![GitHub license](https://img.shields.io/github/license/JackMD/MysteryCrate.svg)](https://github.com/JackMD/MysteryCrate/blob/master/LICENSE)|[![Poggit-CI](https://poggit.pmmp.io/ci.shield/JackMD/MysteryCrate/MysteryCrate)](https://poggit.pmmp.io/ci/JackMD/MysteryCrate/MysteryCrate)|[![](https://poggit.pmmp.io/shield.state/MysteryCrate)](https://poggit.pmmp.io/p/MysteryCrate)|

### A MysteryCrate plugin for PocketMine-MP // McPe 1.2
### Features
 - This plugin adds custom **crates** to your server.
 - Crates can be opened with a custom **key**.
 - Easy to use `crates.yml` for adding custom items to the crate.
 - Upon **opening** the crate **particles** are generated telling player someone opened the crate.
 - Ability to give enchanted item to players.
 - Ability to give custom enchanted items to players. Requires [PiggyCustomEnchants](https://github.com/DaPigGuy/PiggyCustomEnchants) by [@DaPigGuy](https://github.com/DaPigGuy)
 - Fool proof. Players cannot grief it.
 - You can set multiple crates using the pattern set in `crates.yml`.
 - The entire plugin is suited for [PocketMine-MP](https://github.com/pmmp/PocketMine-MP) latest API.
### How to setup?
 - For adding enchants that are not registered by PocketMine-MP you may use **[VanillaEnchantments](https://github.com/TheAz928/VanillaEnchantments)** by [@TheAz928](https://github.com/TheAz928) or **[TeaSpoon](https://github.com/CortexPE/TeaSpoon)** by [@CortexPE](https://github.com/CortexPE) for adding enchants on the items.
 - Additionally you can also add **CustomEnchants** to the items. For this you require **[PiggyCustomEnchants](https://github.com/DaPigGuy/PiggyCustomEnchants)** by [@DaPigGuy](https://github.com/DaPigGuy).
 - Get the [.phar](https://poggit.pmmp.io/ci/JackMD/MysteryCrate/MysteryCrate) and drop the into your `plugins` folder.
 - Next navigate to the `crates.yml` file and edit/set the block you want the crate to be recognized with under `block: "ID:META"` key.
 - **Make sure that name of the world where crate is located is same as the world folder name.**
 - Now mention the `name` of the`world` where the crate is located in `crateWorld`.
 - Reload the server.
 - Place the block down you initially set in `crates.yml` under `block: "ID:META"` key.
 - Place a chest `ID: 54` on top of that block.
 - To open the crate you require a `crateKey`.
 - To get the key use `/key [type] [player] [amount]` in-game and then tap the crate with it. You can set/see the crate type in `crates.yml` file.
### FAQs
**Q: How to setup the crates?**<br />
A: Please read [this](https://github.com/JackMD/MysteryCrate#how-to-setup).<br /><br />
**Q: How to add particles?**<br />
A: Its very simple. When you place a chest on top of the block you set in `crates.yml` the plugin will automatically add its coordinates to the `blocks.yml` file. You will just need to restart the server for the particles to show.<br /><br />
**Q: How to remove the particles?**<br />
A: Just destroy the chest whose particles you want to remove. Restart the server and the particles will be removed.<br /><br />
**Q: Will the crates have floating text above them?**<br />
A: Yes, they will have Floating Text. <br /><br />
**Q: Can I edit the floating text that appears above the crate?**<br />
A: Yes you can. Navigate to `blocks.yml` and edit the crate text you see. For example, you want to edit the floating text of legendary crate then there would be a key\heading of `legendary:` in the `blocks.yml` file. Edit the text after the `:` to edit\change the floating text.<br /><br />
**Q: I found a issue in the plugin what do I do?**<br />
A: Please open an issue [here](https://github.com/JackMD/MysteryCrate/issues) and give as much detail as possible.<br /><br />
**Q: I would like a new feature to be added. How do I report?**<br />
A: Enter the feature in [#34](https://github.com/JackMD/MysteryCrate/issues/34). Give as much detail as possible and I will see what I can come up with.<br /><br />
**Q: What to do with the `blocks.yml` file?**<br />
A: You need not to do anything with it unless you want to edit the floatiing text. The plugin automatically generates stuff within the file to make the setup for the user easier.
### Commands and Permissions
|Description|Command|Permission|Default|
|:--:|:--:|:--:|:--:|
|Crate Key|`/key [type] [player] [amount]`|`mc.command.key`|`op`|
|Use a crate|`~`|`mc.crates.use`|`true`|
|Destroy a crate|`~`|`mc.crates.destroy`|`op`|
|Create a crate|`~`|`mc.crates.create`|`op`|
### TODO's
 - [X] Add basic particles.
 - [X] Add floating text above the crate(s).
 - [X] Add ability to execute commands as a reward.
 - [X] Add custom lore to the rewards.
 - [X] Finish working on commands.
 - [X] Add custom `crates.yml`to declare custom items to be given to players.
 - [X] Make setup a bit easier.
 - [X] Make it so that not random items are generated in all the slots as discussed [#1](https://github.com/JackMD/MysteryCrate/issues/1)
 - [X] Add support for making more than one crate.
 - [X] Add support for normal enchants.
 - [X] Add support for custom enchants.
 - [X] Find more in [#34](https://github.com/JackMD/MysteryCrate/issues/34).
### Info
  - Make sure to subscribe to be updated for when i release more stuff on my [YT](https://youtu.be/x_mc-ocrdDU) channel.
  - Support is appreciated.
  - Please don't hesitate to ask questions or report bug report in issues section.
### Credits
  - [PiggyCrates](https://github.com/DaPigGuy/PiggyCrates) by [@DaPigGuy](https://github.com/DaPigGuy)
### Video
[![YouTube](https://img.youtube.com/vi/x_mc-ocrdDU/0.jpg)](https://youtu.be/x_mc-ocrdDU)
