# lighthousecontrol
Beta Release Version 0.1

lighthouse-control version 1.0
Author Roy Oltmans

This project works only in Linux it combines a couple of features not yet available for PyKaKu.
For this to get working you will need a TPC300 from KlikAanKlikUit

I would like to thank the project PyKaKu this was the basis of this project.

Beforehand I must warn you I am not responsible for any damage to your system, software or Klik Aan Klik Uit hardware or any other hard and software. This is a hobby project where of you cannot get any guarantees or Quality Assurance. The project has been build under license GNU GPL v.3 any work you would like to do on this software is allowed. If you would like to contribute please do I am open for any suggestion.  

In the future I wan't to add feature of the HUE Philips enviroment, so this suite can control all the light units in a house. Practical I learned that when the reach of the emmiter of the TPC300 gets bad the times that the light go ON and OFF are irregulair. In that case a amplifier of KaKu would be preferrable, this solved 2 days of debugging for me.

So for the features, this package has the following:

A webpage based on Jquery icw PHP to make it dynamic and mobile aware
	- I have tested it with a mobile App wraped as hybrid on Android and it works great. I will try and post that part later on (maybe a tutorial).
	- It has not been secured but can be trough several ways (basic authorization on the webserver etc). I hope to add some better security measures in a later fase
	- I would love to add some more categorization etc, right now it is very basic
		- Missing the feature to directly dimm lamps, I will get dimmable sensors later this year so I will add this feature later on
For this to get working the www folder needs to be published via a webserver.

It uses a basic commandline structure where tpc300.py is the core, the following options are used:
 - -c for channel number
 - -l for level (sub options on and off)
 - -s for chene not used is not mandetory
 - -w a wait time before the command is pushed to the device (this primairely a issue when timing multiple request because USB is a serial bus that cannot have multiple requests at the same time)

It has a sunrise_sunset.py scheduling feature (that I will move to a schedular service - haven't got the time for that). 
This means it can schedule tasks via cron with scheduled tpc300 commands when scheduled it cleans cron via a Shell script, this has to be done based on a bug I coudn't fix in Python (as far as I could find this is current behaviour of Python). This has been tested on fedora 20 I am not sure how this will work on other distro, I haven't used any exotic features of fedora.

I made a socket service (socketservice.py this needs to run in the background as a service continuoisly) that can receive TCP requests porting it to tpc300.py this feature is thanks to hades123 from nodo-domotica. Over that a simple Post Get API was created in PHP that accepts the exact same requests and ports this to the socket service. 

To get this working the www folder needs to published on a webserver that can send requests to socketservice on that (or other server running the service) server. This hase not been secured at the moment but should be if run outside of your internal network.

Use the setup.sh for the necessary tools for python to get this project working. Requirements can be found in the requirements file.

