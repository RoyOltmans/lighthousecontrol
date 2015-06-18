__author__ = 'erik@precompiled.com'
import usb.core
import usb.util
import binascii
import syslog, os
#os.environ['PYUSB_DEBUG'] = 'debug'
#os.environ['LIBUSB_DEBUG'] = '3'
#os.environ['PYUSB_LOG_FILENAME'] = 'usb_debug.log'

class TPC300(object):
    """
    This is the pure Python implementation of the TPC300 class.
    While the Windows version uses the TCP300A.DLL that can be
    downloaded, this version sends raw instructions to the USB
    port using pyUSB 1.x.
    It should be usable in any Python environment as it is
    pure Python code, but I found that usb.core.find() does not
    return any USB devices in Windows.
    """
    def __init__(self):
        # find our device - does not seem to work in Windows...
        self.dev = usb.core.find(idVendor=0xFEFF, idProduct=0x0802)

        # was it found?
        if self.dev is None:
            raise ValueError('TPC300 USB Device not found')
        else:
            print self.dev
        self.reset
        self.reset

        # set the active configuration. With no arguments, the first
        # configuration will be the active one
        if self.dev.is_kernel_driver_active(0) is True:
            try:
                self.dev.detach_kernel_driver(0)
            except usb.core.USBError as e:
                sys.exit("Could not detatch kernel driver: %s" % str(e))

        # fixed lock with multiple devices at the same time
        str_error = "Not executed yet."
        while str_error:
            try:
                self.dev.set_configuration()
                str_error=None
            except Exception as str_error:
                pass

        # get an endpoint instance: device -> config -> interface -> endpoint
        self.cfg = self.dev.get_active_configuration()
        self.iep, self.oep = self._get_eps()

    def _write_oep(self, data):
        syslog.syslog("Writing Command to USB")
        syslog.syslog(str("Command: " + binascii.hexlify(data)))
        self.oep.write(data)

    def reset(self):
        # NOTE: [http://libusb.sourceforge.net/doc/function.usbreset.html]
        # Causes re-enumeration: After calling usb_reset, the device will
        # need to re-enumerate and thusly, requires you to find the new device
        # and open a new handle. The handle used to call usb_reset will no
        # longer work.
        self.dev.reset()
        #self.start()

    def _get_eps(self):
        interface_number = self.cfg[(0, 0)].bInterfaceNumber
        # alternate_setting = usb.control.get_interface(self.dev, interface_number)
        self.intf = intf = usb.util.find_descriptor(
            self.cfg, bInterfaceNumber = interface_number,
        )

        iep = usb.util.find_descriptor(
            intf,
            custom_match = \
            lambda e: \
                usb.util.endpoint_direction(e.bEndpointAddress) == \
                usb.util.ENDPOINT_IN
        )
        oep = usb.util.find_descriptor(
            intf,
            custom_match = \
            lambda e: \
                usb.util.endpoint_direction(e.bEndpointAddress) == \
                usb.util.ENDPOINT_OUT
        )
        assert all((iep, oep))
        return iep, oep

    def send(self, signaltype, code, onoff):
        # bit[0] = instruction(0x5A)
        # bit[1] = code
        # bit[2] = onoff*2 + signaltype
        instruction = 90
        command = [instruction, code, (onoff*2), 19,20,17,5]
        padding = [0] * (64 - len(command))
        # fixed lock with multiple devices at the same time
        str_error = "Not executed yet."
        while str_error:
            try:
                self._write_oep(bytearray(command + padding))
                str_error=None
            except Exception as str_error:
                pass
        return str("command" + binascii.hexlify(bytearray(command+  padding)))

    def scene(self, number):
        # bit[0] = instruction(0x53)
        # bit[1] = (scene -1)
        instruction = 83
        command = [instruction, number-1]
        padding = [0] * (64 - len(command))
        self._write_oep(bytearray(command + padding))

    def __del__(self):
        self.close()

    def close(self):
        usb.util.release_interface(self.dev, self.intf)