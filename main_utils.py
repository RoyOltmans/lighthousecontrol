#-------------------------------------------------------------------------------
# Name:        main_Utils
# Purpose:     Class functions for fetching data scheduling KaKu tasks
#
# Author:      roy.oltmans
#
# Created:     23-10-2014
# Copyright:   (c) roy.oltmans 2014
# Licence:     <your licence>
#-------------------------------------------------------------------------------

import os, subprocess, syslog
import xml.etree.ElementTree as ET, json, urllib
import MySQLdb, ConfigParser
from crontab import CronTab

class tools(object):
    #setup command to call kaku tpc300 for on of switch on channel X
    def command(self, scene,channel,status,waittime=0):
        if scene == 0:
            kakuCommand = '/opt/pykaku/tpc300.py -c ' + str(channel) + ' -l ' + str(status)
        else:
            kakuCommand = '/opt/pykaku/tpc300.py -s ' + str(scene) + ' -c ' + str(channel) + ' -l ' + str(status)
        if waittime >= 1:
            waittime = str(waittime)
            kakuCommand = kakuCommand + ' -w ' + waittime
        return kakuCommand

    #set cron schedule
    def setKakuSchedule(self, description,date,time,scene,channel,status,user,waittime=0):
        command = self.command(scene,channel,status,waittime)
        self.mysql("DELETE FROM kaku_schedule WHERE taskdesc = '" + description + "'")
        self.mysql("INSERT INTO kaku_schedule (taskdesc, date, time, state, command) VALUES ('" + description + "','"+date+"','"+time+"',0,'"+command+"')")
        timelst = time.split(':')
        timehr = timelst[0]
        timemn = timelst[1]
        self.cronSchedule(1,'root',timemn,timehr,description,command)

    #get XML from URL Y
    def getXML(self, url):
        try:
            uh = urllib.urlopen(url)
        except urllib.error.URLError as e:
            print(e.reason)
            sendMail('roy@oltmans.nl','roy@oltmans.nl','Cannot fetch XML from','URL:'+url+'\r Reason:'+e.reason)
            sys.exit(2)
        data = uh.read()
        XMLdata = ET.fromstring(data)
        print 'Retrieving', url
        print 'XML blocks:', len(XMLdata)
        print 'Retrieved',len(data),'characters'
        return XMLdata

    #send mail
    def sendMail(self, mailfrom,mailto,mailsubject,mailbody):
        sendmail_location = "/usr/sbin/sendmail" # sendmail location
        p = os.popen("%s -t" % sendmail_location, "w")
        p.write("From: %s\n" % mailfrom)
        p.write("To: %s\n" % mailto)
        p.write("Subject: " + mailsubject + "\n")
        p.write("\n") # blank line separating headers from body
        p.write(mailbody)
        status = p.close()
        if status != 0:
           print "Sendmail exit status", status

    #set the real cronjobs
    def cronSchedule(self, enable,user_,minutes,hours,comment_,cmd):
        tab = CronTab(user=user_)
        if enable == 1:
            #create crons
            tab.remove_all(comment=comment_)
            cron_job = tab.new(cmd, comment=comment_)
            cron_job.minute.on(minutes)
            cron_job.hour.on(hours)
            tab.write()
            syslog.syslog(tab.render())
        elif enable == 0:
            #remove crons based on comment
            tab.remove_all(comment=comment_)
            tab.write()
            syslog.syslog(tab.render())
        cleanCrontab = os.path.dirname(os.path.abspath(__file__)).replace(' ','\ ') + "/cleancrontab.sh " + user_ #remove spaces from crontab
        print "crontabclean: " + cleanCrontab
        try:
            os.system(cleanCrontab)
        except OSError:
            print 'Error executing Command: '+ cleanCrontab

    #Fetch Configuration
    def fetchConfig(self):
        Config = ConfigParser.ConfigParser()
        ConfigFilePath = os.path.dirname(os.path.abspath(__file__)).replace(' ','\ ') + "/config.ini"
        Config.read(ConfigFilePath)
        return Config

    #Make a MySQL connection
    def mysql(self, myquery):
        config = self.fetchConfig()
        myhost = config.get('MySQL', 'Host')
        myuser = config.get('MySQL', 'User')
        mypasswd = config.get('MySQL', 'Password')
        myschema = config.get('MySQL', 'Schema')
        conn = MySQLdb.connect (myhost,myuser,mypasswd,myschema)
        cursor = conn.cursor()
        try:
            cursor.execute(myquery)
            print "Query: " + myquery
        except Exception, e:
            print repr(e)
            print "Query fault: " + myquery
        result = cursor.fetchone()
        cursor.close()
        conn.commit()
        conn.close()
        return result