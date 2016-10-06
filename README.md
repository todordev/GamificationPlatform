Gamification Platform for Joomla! 
==========================
( Version 2.1.1 )
- - -

Gamification Platform is a Joomla! extension that provides functionality, API and resources which can be used to gamify many Joomla! extensions.

##Documentation
You can find documentation on following pages.

[Documentation and FAQ](http://itprism.com/help/101-gamification-platform-documentation)

[API documentation](http://cdn.itprism.com/api/gamification/index.html)

##Download
You can [download Gamification Platform package](http://itprism.com/free-joomla-extensions/ecommerce-gamification/game-mechanics-platform) from the website of ITPrism.

You can also download it preconfigured from the [distribution repository](https://github.com/ITPrism/GamificationDistribution)

##License
Gamification Platform is under [GPLv3 license](http://www.gnu.org/licenses/gpl-3.0.en.html).

## About the code in this repository
This repository contains code that you should use to create a package. You will be able to install that package via [Joomla extension manager](https://docs.joomla.org/Help25:Extensions_Extension_Manager_Install).

##How to create a package?
* You should install [ANT](http://ant.apache.org/) on your PC.
* Download or clone [Gamification Platform distribution](https://github.com/ITPrism/GamificationDistribution).
* Download or clone the code from this repository.
* Rename the file __build/example.txt__ to __build/antconfig.txt__.
* Edit the file __build/antconfig.txt__. Enter name and version of your package. Enter the path to the folder of Gamification Platform distribution. Enter the path to the folder where the source code of the package will be stored (the folder where you have saved this repository).
* Save the file __build/antconfig.txt__.
* Open a console and go in folder __build__.
* Type `ant` and click enter. The system will copy all files from distribution to the folder where you are going to build an installable package.

`ant`

##Contribute
If you would like to contribute to the project you should use Gamification Platform distribution. That repository provides Joomla CMS and Gamification Platform - installed and configured for development.
You can clone it on your PC and install it on your localhost. You should use it as development environment. You should use it to create branches, to add new features, to fix issues and to send pull request.
