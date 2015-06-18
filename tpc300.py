#!/usr/bin/env python
import usbcontroller, main_utils
import sys, syslog, getopt, time

def main(argv):
    # Load kaku objects and utils
    myKaku = usbcontroller.TPC300()
    tools = main_utils.tools()
    # set channel 3 to level 11
    # only works for dimmers, for plain switches just use myKaku.send(1,3,16)
    InputChannel = ''
    InputLevel = ''
    InputScene = 1
    InputSceneOption = ''
    WaitTime = 0
    try:
        opts, args = getopt.getopt(argv,"h:s:c:l:e:w:",["scene","channel","level","inputscene"])
    except getopt.GetoptError:
        print 'tpc300.py -s <scenenumber> -c <channelnumber> -l <levelnumber>'
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            print 'tpc300.py -s <scenenumber> -c <channelnumber> -l <levelnumber>'
            sys.exit()
        elif opt in ("-s", "--scene"):
            InputScene = int(arg)
        elif opt in ("-c", "--channel"):
                InputChannel = int(arg)
        elif opt in ("-l", "--level"):
            if arg == 'on':
                InputLevel = int(17)
            elif arg == 'off':
                InputLevel = 00
            else:
                InputLevel = int(arg)
        elif opt in ("-e", "--inputscene"):
                InputSceneOption = int(arg)
        elif opt in ("-w", "--waittime"):
                if int(arg) >= 1:
                    WaitTime = int(arg)
                    time.sleep(WaitTime)
    output = 'Scene=', InputScene, 'Channel=', InputChannel, 'Level=', InputLevel
    print output
    myKaku.send(InputScene,InputChannel,InputLevel)
    time.sleep(1)
    syslog.syslog(str(output))

    # execute scene 1
    if InputSceneOption == 1:
        myKaku.scene(InputScene)
        time.sleep(9)
    syslog.syslog('Scene Set')
    tools.mysql('INSERT INTO kaku_status' \
                '(channel, scene, level) '\
                'VALUES ('+str(InputChannel)+','+str(InputScene)+','+str(InputLevel)+') '\
                'ON DUPLICATE KEY UPDATE '\
                'scene = VALUES(scene), '\
                'level = VALUES(level)')

if __name__ == '__main__':
    main(sys.argv[1:])