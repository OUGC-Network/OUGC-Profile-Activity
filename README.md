<p align="center">
    <a href="" rel="noopener">
        <img width="700" height="400" src="https://github.com/OUGC-Network/OUGC-Profile-Activity/assets/1786584/43e1a95c-ffa2-4c7f-9174-65db36913565" alt="Project logo">
    </a>
</p>

<h3 align="center">OUGC Profile Activity</h3>

<div align="center">

[![Status](https://img.shields.io/badge/status-active-success.svg)]()
[![GitHub Issues](https://img.shields.io/github/issues/OUGC-Network/OUGC-Profile-Activity.svg)](./issues)
[![GitHub Pull Requests](https://img.shields.io/github/issues-pr/OUGC-Network/OUGC-Profile-Activity.svg)](./pulls)
[![License](https://img.shields.io/badge/license-GPL-blue)](/LICENSE)

</div>

---

<p align="center"> Show an overview of latest user threads or posts in profiles.
    <br> 
</p>

## 📜 Table of Contents <a name = "table_of_contents"></a>

- [About](#about)
- [Getting Started](#getting_started)
    - [Dependencies](#dependencies)
    - [File Structure](#file_structure)
    - [Install](#install)
    - [Update](#update)
    - [Template Modifications](#template_modifications)
- [Settings](#settings)
- [Usage](#usage)
- [Built Using](#built_using)
- [Authors](#authors)
- [Acknowledgments](#acknowledgement)
- [Support & Feedback](#support)

## 🚀 About <a name = "about"></a>

OUGC Profile Activity by OUGC.Network transforms user profiles by showcasing the user's latest threads or posts,
offering a glimpse into their forum activity at a glance. With engaging previews of the content message directly within
profiles, enhance user interactions and foster a sense of community. With the flexibility to exclude specific forums
from the content, tailoring the displayed content to suit your forum's preferences. With this plugin, create a vibrant
forum community where users feel connected and engaged from the moment they visit a profile.

[Go up to Table of Contents](#table_of_contents)

## 📍 Getting Started <a name = "getting_started"></a>

The following information will assist you into getting a copy of this plugin up and running on your forum.

### Dependencies <a name = "dependencies"></a>

A setup that meets the following requirements is necessary to use this plugin.

- [MyBB](https://mybb.com/) >= 1.8
- PHP >= 7.0
- [PluginLibrary for MyBB](https://github.com/frostschutz/MyBB-PluginLibrary) >= 13

### File structure <a name = "file_structure"></a>

  ```
   .
   ├── inc
   │ ├── plugins
   │ │ ├── ougc
   │ │ │ ├── ProfileActivity
   │ │ │ │ ├── Hooks
   │ │ │ │ │ ├── Admin.php
   │ │ │ │ │ ├── Forum.php
   │ │ │ │ ├── Templates
   │ │ │ │ │ ├── PostIcon.html
   │ │ │ │ │ ├── PostsRow.html
   │ │ │ │ │ ├── PostsRowEmpty.html
   │ │ │ │ │ ├── PostsTable.html
   │ │ │ │ │ ├── ThreadsRow.html
   │ │ │ │ │ ├── ThreadsRowEmpty.html
   │ │ │ │ │ ├── ThreadsTable.html
   │ │ │ │ ├── settings.json
   │ │ │ │ ├── Admin.php
   │ │ │ │ ├── Core.php
   │ │ ├── ougc_profileactivity.php
   │ ├── languages
   │ │ ├── english
   │ │ │ ├── admin
   │ │ │ │ ├── ougc_profileactivity.lang.php
   │ │ │ ├── ougc_profileactivity.lang.php
   ```

### Installing <a name = "install"></a>

Follow the next steps in order to install a copy of this plugin on your forum.

1. Download the latest package from the [MyBB Extend](https://community.mybb.com/mods.php?action=view&pid=PID) site or
   from
   the [repository releases](https://github.com/OUGC-Network/OUGC-Profile-Activity/releases/latest).
2. Upload the contents of the _Upload_ folder to your MyBB root directory.
3. Browse to _Configuration » Plugins_ and install this plugin by clicking _Install & Activate_.

### Updating <a name = "update"></a>

Follow the next steps in order to update your copy of this plugin.

1. Browse to _Configuration » Plugins_ and deactivate this plugin by clicking _Deactivate_.
2. Follow step 1 and 2 from the [Install](#install) section.
3. Browse to _Configuration » Plugins_ and activate this plugin by clicking _Activate_.

### Template Modifications <a name = "template_modifications"></a>

To display the activity content in profiles it is required that you edit the `member_profile` template for each of your
themes.

1. Open the `member_profile` template for editing.
2. Add `{$memprofile['ougcProfileActivityThreads']}` and `{$memprofile['ougcProfileActivityPosts']}`
   after `{$contact_details}`.
3. Save the template.

Alternatively, you could place these variables anywhere within the `member_profile` template.

[Go up to Table of Contents](#table_of_contents)

## 🛠 Settings <a name = "settings"></a>

Below you can find a description of the plugin settings.

### Global Settings

- **Maximum Threads To Display** `numeric`
    - _Select how many latest threads to display in profiles. Select <code>0</code> to disable._
- **Maximum Posts To Display** `numeric`
    - _Select how many latest posts to display in profiles. Select <code>0</code> to disable._
- **Ignored Thread Forums** `select` Default: _none_
    - _Select what forums to ignore when building the latest threads activity content._
- **Ignored Post Forums** `select` Default: _none_
    - _Select what forums to ignore when building the latest posts activity content._
- **Maximum Subject Character Length** `numeric` Default: `100`
    - _You can strip the thread or post subject to fit specific widths._

[Go up to Table of Contents](#table_of_contents)

## 📖 Usage <a name="usage"></a>

### Custom Moderation Tools

This plugin has no additional configurations; after activating make sure to modify the global settings in order to get
this plugin working.

[Go up to Table of Contents](#table_of_contents)

## ⛏ Built Using <a name = "built_using"></a>

- [MyBB](https://mybb.com/) - Web Framework
- [MyBB PluginLibrary](https://github.com/frostschutz/MyBB-PluginLibrary) - A collection of useful functions for MyBB
- [PHP](https://www.php.net/) - Server Environment

[Go up to Table of Contents](#table_of_contents)

## ✍️ Authors <a name = "authors"></a>

- [@Omar G](https://github.com/Sama34) - Idea & Initial work

See also the list of [contributors](https://github.com/OUGC-Network/OUGC-Profile-Activity/contributors) who participated
in this project.

[Go up to Table of Contents](#table_of_contents)

## 🎉 Acknowledgements <a name = "acknowledgement"></a>

- [The Documentation Compendium](https://github.com/kylelobo/The-Documentation-Compendium)

[Go up to Table of Contents](#table_of_contents)

## 🎈 Support & Feedback <a name="support"></a>

This is free development and any contribution is welcome. Get support or leave feedback at the
official [MyBB Community](https://community.mybb.com/thread-221815.html).

Thanks for downloading and using our plugins!

[Go up to Table of Contents](#table_of_contents)