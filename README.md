## SmartBB ##

SmartBB is an open source minimalist Bulletin Board System (BBS), where you can easily exchange with other people :
Its goal is to provide an easy-to-install BBS for everybody (just clone and follow the instructions), in order to setup in few minutes
a place where people can meet to exchange about anything.  
![Home page](https://raw.githubusercontent.com/f1she3/SmartBB/master/screenshots/Home%20page.png?raw=true "SmartBB")
![Article & comments](https://github.com/f1she3/SmartBB/blob/master/screenshots/article%20&%20comments.png?raw=true "Article")

SmartBB's code is :

- simple
- clean & secured
- all coded by me :)

The administration system is just the same, it's simple but efficient.
![Admin page](https://raw.githubusercontent.com/f1she3/SmartBB/master/screenshots/Admin%20page.png?raw=true "SmartBB")

Please feel free to tell me your thoughts about it, to report any problem and to improve it :)

### Components ###
>	#### Backend ####
>		- PHP
>		- JavaScript + JQuery
> 		- MySQL
>	#### Frontend ####
>		- HTML5
>		- CSS3
>		- Twitter Bootstrap
## Installation ##
```
git clone https://github.com/f1she3/SmartBB.git
```
- Install & start [Nginx](https://nginx.org/) and [MySQL](https://mariadb.org/): 
  ### Debian ###
  `sudo apt install nginx mysql-server`
  ### Arch Linux ###
  `sudo pacman -S nginx mariadb`

- Copy nginx.conf to your config file
- Import the database `smartbb.sql`
- Edit `functions/init.php` according to your needs and your configuration

Happy sharing !
