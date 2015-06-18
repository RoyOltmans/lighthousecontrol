#!/usr/bin/env python

#-------------------------------------------------------------------------------
# Name:        Sunrise Sunset voor de buitenlamp
# Purpose:     Hoofdaansturing voor verlichting buiten het huis
#
# Author:      roy.oltmans
#
# Created:     22-10-2014
# Copyright:   (c) roy.oltmans 2014
# Licence:     <your licence>
#-------------------------------------------------------------------------------

import main_utils
import datetime

def main():
    tools = main_utils.tools()
    datetomorrow = datetime.date.today() + datetime.timedelta(days=1)
    url = 'http://www.earthtools.org/sun-1.0/52.091423/5.018133/'+str(datetomorrow.day)+'/'+str(datetomorrow.month)+'/99/0' #External call to earthools free
    sunsr = tools.getXML(url)
    morning = sunsr.findall('morning')
    evening = sunsr.findall('evening')
    for item in morning:
        sunriselst = item.find('sunrise').text.split(':')
        sunrsbase = datetime.datetime(100,1,1,int(sunriselst[0]),int(sunriselst[1]),00) #make aerthtime calculatable
        sunrsdelta = str(sunrsbase + datetime.timedelta(0,1800)).split(' ') # add half an hour to dawn (time given back
        if str(sunrsbase - datetime.datetime(100,1,1,6,00,00)).split()[0] == '-1': #No negative -1 difference between 0600 in te morning (lamp cannot go off before going on)
            sunriseEnable = 0
        else:
            sunriseEnable = 1
    for item in evening:
        timesunset = item.find('sunset').text

    #Make Schedules
    tools.cronSchedule(1,'root',00,5,'Buitenlampen schedule','/opt/pykaku/sunset_sunrise.py')
    tools.setKakuSchedule("Buitenlamp voordeur savonds uit",str(datetomorrow.isoformat()),'23:30:00',0,4,'off','root')
    tools.setKakuSchedule("Buitenlamp voordeur savonds aan",str(datetomorrow.isoformat()),timesunset,0,4,'on','root')
    tools.setKakuSchedule("Buitenlamp tuin savonds uit",str(datetomorrow.isoformat()),'23:30:00',0,5,'off','root',12)
    tools.setKakuSchedule("Buitenlamp tuin savonds aan",str(datetomorrow.isoformat()),timesunset,0,5,'on','root',12)
    tools.setKakuSchedule("Buitenlamp achterdeur savonds uit",str(datetomorrow.isoformat()),'23:30:00',0,6,'off','root',6)
    tools.setKakuSchedule("Buitenlamp achterdeur savonds aan",str(datetomorrow.isoformat()),timesunset,0,6,'on','root',6)
    if sunriseEnable == 1:
        tools.setKakuSchedule("Buitenlamp voordeur sochtends aan",str(datetomorrow.isoformat()),'06:00:00',0,4,'on','root')
        tools.setKakuSchedule("Buitenlamp voordeur sochtends uit",str(datetomorrow.isoformat()),str(sunrsdelta[1]),0,4,'off','root')
        tools.setKakuSchedule("Buitenlamp tuin sochtends aan",str(datetomorrow.isoformat()),'06:00:00',0,5,'on','root',6)
        tools.setKakuSchedule("Buitenlamp tuin sochtends uit",str(datetomorrow.isoformat()),str(sunrsdelta[1]),0,5,'off','root',6)
    else:
        tools.cronSchedule(0,'root',0,0,'Buitenlamp voordeur sochtends aan','')
        tools.cronSchedule(0,'root',0,0,'Buitenlamp voordeur sochtends uit','')

if __name__ == '__main__':
    main()