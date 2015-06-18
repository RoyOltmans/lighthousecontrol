#!/usr/bin/env python
import time
import os, socket, threading, syslog

# IP socket thread
class ClientThread ( threading.Thread ):

    # Override Thread's __init__ method to accept the parameters needed:
    def __init__ ( self ):
       self.data = 'No Data'
       threading.Thread.__init__ ( self )

    def run ( self ):
       self.accept()

    def retdata ( self ):
       return self.data

    def setdata ( self, text ):
       self.data = text

    def accept ( self ):
       while True:
          self.channel, self.details = server.accept()
          self.receive()

    def receive ( self ):
       print 'Received connection:', self.details [ 0 ]
       self.data = self.channel.recv ( 1024 )
       self.channel.close()
       print 'receive: ', self.data
       print 'Closed connection:', self.details [ 0 ]

input = ''
server = socket.socket ( socket.AF_INET, socket.SOCK_STREAM )
server.bind ( ( '', 50007 ) )
server.listen ( 5 )
ip=ClientThread()
ip.start()


while True:
    #x=0;
    if ip.retdata() != 'No Data':
        print ip.retdata()
        parlist = ip.retdata().split(',')
        parameters = "-c " + parlist[0] + " -l " + parlist[1]
        print parameters
        command = os.path.dirname(os.path.abspath(__file__)) + "/tpc300.py " + parameters
        syslog.syslog('TCP Kaku service commands: '+ command)
        try:
             os.system(command)
        except OSError:
             syslog.syslog('Error executing Command: '+ command)
        ip.setdata('No Data')
    time.sleep(1)
